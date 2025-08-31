import React, { useState, useEffect }from "react";

import { createRoot } from "react-dom/client";
import { router } from "@inertiajs/react";
import axios from "axios";
import { jsPDF } from "jspdf";
import { PDFDocument } from "pdf-lib";
import { Button, Tooltip, DialogActions, DialogContent, DialogContentText, Alert } from "@mui/material";
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import AddIcon from '@mui/icons-material/Add';
import FileDownloadIcon from '@mui/icons-material/FileDownload';
import SendIcon from '@mui/icons-material/Send';
import SendToMobileIcon from '@mui/icons-material/SendToMobile';
import ReviewsIcon from '@mui/icons-material/Reviews';
import HistoryIcon from '@mui/icons-material/History';
import PrecisionManufacturingIcon from '@mui/icons-material/PrecisionManufacturing';
import SaveIcon from '@mui/icons-material/Save';
import LogoutIcon from '@mui/icons-material/Logout';
import HowToRegIcon from '@mui/icons-material/HowToReg';
import KeyboardReturnIcon from '@mui/icons-material/KeyboardReturn';
import AssignmentReturnIcon from '@mui/icons-material/AssignmentReturn';
import ModeEditIcon from '@mui/icons-material/ModeEdit';
import AssignmentTurnedInIcon from '@mui/icons-material/AssignmentTurnedIn';

import { useDialogContext } from '../../Context/useDialogContext';
import { useRequisitionContext } from '../../Features/RequisitionDetail/useRequisitionContext';
import { useUser } from "../../Context/useUserContext";
import ReviewerPicker from "../../Features/RequisitionDetail/ReviewerPicker";
import ActionSuccessful from "../../Dialogs/ActionSuccessful";
import AddRoleDialog from "../../Features/Admin/AddRoleDialog";
import RequisitionsPeriodDialog from "../../Features/Admin/RequisitionsPeriodDialog";
import SubmitResultDialog from "../../Features/RequisitionDetail/SubmitResultDialog";
import RequisitionDetailExport from "../../Features/RequisitionDetail/RequisitionDetailExport";

let buttonComponentList = {};

buttonComponentList.add_role = ({ styles }) => {
    const { user } = useUser();

    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();
    function handleClick() {
        setDialogTitle('Adicionar papel');
        setDialogBody(<AddRoleDialog user={user}/>);
        openDialog();
    }
    return (
        <Button
            key="add_role"
            onClick={handleClick}
            {...styles}
        >
            Adicionar um papel
        </Button>
    );
};

buttonComponentList.admin = ({ styles }) => (
    <Button
        key="admin"
        href={route('admin')}
        startIcon={<AdminPanelSettingsIcon />}
        {...styles}
    >
        Administrar Sistema
    </Button>
);

buttonComponentList.automatic_requisition = ({ styles }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('automaticDeferral'),
                { 'requisitionId': requisitionData.id },
                {
                    onSuccess: (resp) => {
                        closeDialog();
                        setDialogTitle('Deferimento automático realizado');
                        setDialogBody(<ActionSuccessful dialogText={'O deferimento automático foi realizado com sucesso.'} />)
                        router.get(route('list'));
                    },
                    onError: (error) => {
                        closeDialog();
                    }
                });
        };
        setDialogBody(
            <>
                <DialogContent>
                    <DialogContentText>
                        Tem certeza de que quer realizar o deferimento automático?
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button color="error" onClick={closeDialog}>
                        Cancelar
                    </Button>
                    <Button variant="contained" onClick={submitAndReturnToList}>
                        Confirmar
                    </Button>
                </DialogActions>
            </>
        );
        openDialog();
    };
    return (
        <Button
            key="automatic_requisition"
            onClick={handleClick}
            startIcon={<PrecisionManufacturingIcon />}
            {...styles}
        >
            Deferimento Automático
        </Button>
    );
};

buttonComponentList.edit_requisition = ({ styles = {} }) => {
    const { isRole } = useUser();
    const { requisitionData } = useRequisitionContext();
    const [isUpdateEnabled, setIsUpdateEnabled] = useState(false);

    useEffect(() => {
        let mounted = true;
        axios.get(route('getRequisitionPeriodStatus'))
            .then((response) => {
                if (mounted) setIsUpdateEnabled(response.data.isUpdateEnabled);
            })
            .catch(() => {
                if (mounted) setIsUpdateEnabled(false);
            });
        return () => { mounted = false; };
    }, []);

    const isButtonEnabled = (isUpdateEnabled && requisitionData.editable) || !isRole(1);

    return (
        <Tooltip
            title="Edição não permitida"
            disableHoverListener={isButtonEnabled}
        >
            <span>
                <Button
                    key="edit_requisition"
                    disabled={!isButtonEnabled}
                    href={route('updateRequisition.get', { 'requisitionId': requisitionData.id })}
                    startIcon={<ModeEditIcon />}
                    {...styles}
                >
                    Editar Requerimento
                </Button>
            </span>
        </Tooltip>
    );
};

buttonComponentList.go_back = ({ styles = {} }) => (
    <Button
        key="go_back"
        startIcon={<KeyboardReturnIcon />}
        onClick={() => window.history.back()}
        {...styles}
    >
        Voltar
    </Button>
);

buttonComponentList.new_requisition = ({ styles = {} }) => {
    const { isRole } = useUser();
    const [isCreationEnabled, setIsCreationEnabled] = useState(false);

    useEffect(() => {
        let mounted = true;
        axios.get(route('getRequisitionPeriodStatus'))
            .then((response) => {
                if (mounted) setIsCreationEnabled(response.data.isCreationEnabled);
            })
            .catch(() => {
                if (mounted) setIsCreationEnabled(false);
            });
        return () => { mounted = false; };
    }, []);

    return (
        <Tooltip
            title="Disponível durante o período de requerimentos"
            disableHoverListener={isCreationEnabled || !isRole(1)}
        >
            <span>
                <Button
                    key="new_requisition"
                    disabled={!isCreationEnabled && isRole(1)}
                    href={route('newRequisition.get')}
                    startIcon={<AddIcon />}
                    {...styles}
                >
                    Criar Requerimento
                </Button>
            </span>
        </Tooltip>
    );
}

buttonComponentList.export = ({ styles = {} }) => (
    <Button
        key="export"
        href={route('exportRequisitionsGet')}
        startIcon={<FileDownloadIcon />}
        {...styles}
    >
        Exportar Lista
    </Button>
);

buttonComponentList.export_current = ({ styles = {} }) => {
    const { requisitionData } = useRequisitionContext();

    const printDocument = async () => {
        const pdf = new jsPDF();

        const container = document.createElement('div');
        const root = createRoot(container);
        root.render(
            <RequisitionDetailExport
                requisition={requisitionData}
            />
        );

        let docs = [];
        for (let doc of requisitionData.documents) {
            const blob = await axios.get(
                route('documents.view', { 'id': doc.id }),
                {
                    responseType: 'blob'
                }
            );
            docs.push(blob.data);
        }

        pdf.html(container, {
            html2canvas: {
                scale: 0.2 // Adjust this scale factor to shrink/grow the content.
            },
            callback: async (pdfInstance) => {
                // Get the PDF blob.
                docs.unshift(pdfInstance.output('blob'));

                const mergedPdf = await PDFDocument.create();

                for (const blob of docs) {
                    const arrayBuffer = await blob.arrayBuffer();
                    const pdf = await PDFDocument.load(arrayBuffer);
                    const copiedPages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    copiedPages.forEach(page => mergedPdf.addPage(page));
                }

                const mergedPdfBytes = await mergedPdf.save();
                const mergedBlob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
                // I'll just shove the logic in here I guess
                const url = URL.createObjectURL(mergedBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = "requisition.pdf";
                link.click();
            },
            x: 0,
            y: 0,
        });
    };
    return (
        <Button
            key="export_current"
            startIcon={<FileDownloadIcon />}
            onClick={printDocument}
            {...styles}
        >
            Exportar Requerimento
        </Button>
    )
};

buttonComponentList.exit = ({ styles = {} }) => (
    <Button
        key="exit"
        href={route('logout')}
        startIcon={<LogoutIcon />}
        {...styles}
    >
        Sair
    </Button>
);

buttonComponentList.registered = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('registered'),
                { 'requisitionId': requisitionData.id },
                {
                    onSuccess: (resp) => {
                        console.log(resp);
                        closeDialog();
                        setDialogTitle('Marcado com sucesso');
                        setDialogBody(<ActionSuccessful dialogText={'O requerimento foi marcado como "Registrado no Júpiter".'} />)
                        openDialog();
                    },
                    onError: (error) => {
                        console.log(error);
                        closeDialog();
                    }
                });
        };
        setDialogBody(
            <>
                <DialogContent>
                    <DialogContentText>
                        Tem certeza de que quer marcar como "Registrado"?
                    </DialogContentText>
                    <Alert severity="warning">Marque o requerimento <strong>após</strong> registrar o parecer no Júpiter.</Alert>
                </DialogContent>
                <DialogActions>
                    <Button color="error" onClick={closeDialog}>
                        Cancelar
                    </Button>
                    <Button variant="contained" onClick={submitAndReturnToList}>
                        Confirmar
                    </Button>
                </DialogActions>
            </>
        );
        openDialog();
    };

    return (
        <Button
            key="registered"
            onClick={handleClick}
            startIcon={<HowToRegIcon />}
            {...styles}
        >
            Registrado no Jupiter
        </Button>
    )
};

buttonComponentList.requisition_history = ({ styles = {} }) => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Button
            key="requisition_history"
            href={route('record.requisition', { 'requisitionId': requisitionData.id })}
            startIcon={<HistoryIcon />}
            {...styles}
        >
            Histórico do Requerimento
        </Button>
    );
};

buttonComponentList.requisition_period = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();
    function handleClick() {
        axios.get(route('getRequisitionPeriodStatus'))
            .then((response) => {
                const { isUpdateEnabled, isCreationEnabled } = response.data;
                setDialogTitle('Configuração do período de Requerimentos');
                setDialogBody(<RequisitionsPeriodDialog isUpdateEnabled={isUpdateEnabled} isCreationEnabled={isCreationEnabled} />);
                openDialog();
            })
            .catch((error) => {
                console.error('Error fetching requisition period status:', error);
            });
    }
    return (
        <Button
            key="requisition_period"
            onClick={handleClick}
            {...styles}
        >
            Período de requerimentos
        </Button>
    );
};

buttonComponentList.reviews = ({ styles = {} }) => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Button
            key="reviews"
            href={route('reviewer.reviews', { 'requisitionId': requisitionData.id })}
            startIcon={<ReviewsIcon />}
            {...styles}
        >
            Pareceres dados
        </Button>
    );
};

buttonComponentList.save = ({ styles = {} }) => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Button
            key="save"
            href={route('record.requisition', { 'requisitionId': requisitionData.id })}
            startIcon={<SaveIcon />}
            {...styles}
        >
            Salvar alterações
        </Button>
    );
};

buttonComponentList.send_to_department = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleSubmit = () => {
        setDialogBody(
            <>
                <DialogContent>
                    <DialogContentText>
                        Enviando...
                    </DialogContentText>
                </DialogContent>
            </>
        );
        openDialog();

        router.post(
            route('sendToDepartment'),
            {
                'requisitionId': requisitionData.id
            },
            {
                onSuccess: (page) => {
                    setDialogTitle('Requerimento enviado');
                    setDialogBody(<ActionSuccessful dialogText={'Enviado ao departamento com sucesso.'} />);
                    openDialog();
                },
                onError: (errors) => console.log(errors)
            }
        );
    }
    return (
        <Button
            key="send_to_department"
            onClick={handleSubmit}
            startIcon={<SendIcon />}
            {...styles}
        >
            Enviar para o Departamento
        </Button>
    );
};

buttonComponentList.send_to_reviewers = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();
    const handleClick = () => {
        axios.get(route('reviewer.reviewerPick', { requisitionId: requisitionData.id }))
            .then((response) => {
                setDialogTitle('Lista de pareceristas');
                setDialogBody(
                    <ReviewerPicker
                        requisitionId={requisitionData.id}
                        reviewers={response.data}
                        closeDialog={closeDialog}
                    />
                );
                openDialog();
            }
        );
    }
    return (
        <Button
            key="send_to_reviewers"
            onClick={handleClick}
            startIcon={<SendToMobileIcon />}
            {...styles}
        >
            Enviar para Pareceristas
        </Button>
    );
};

buttonComponentList.submit_review = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleClick = () => {
        setDialogTitle('Parecer');
        setDialogBody(<SubmitResultDialog requisitionId={requisitionData.id} type="review" submitRoute="submitReview" />);
        openDialog();
    };

    return (
        <Button
            key="submit_review"
            onClick={handleClick}
            startIcon={<AssignmentReturnIcon />}
            {...styles}
        >
            Enviar parecer
        </Button>
    )
};

buttonComponentList.result = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleClick = () => {
        setDialogTitle('Resultado');
        setDialogBody(<SubmitResultDialog requisitionId={requisitionData.id} />);
        openDialog();
    };

    return (
        <Button
            key="result"
            onClick={handleClick}
            startIcon={<AssignmentTurnedInIcon />}
            {...styles}
        >
            Dar resultado
        </Button>
    )
};

buttonComponentList.send_back_to_sg = ({ styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();
    const { requisitionData } = useRequisitionContext();

    const handleSubmit = () => {
        setDialogBody(
            <>
                <DialogContent>
                    <DialogContentText>
                        Enviando...
                    </DialogContentText>
                </DialogContent>
            </>
        );
        openDialog();

        router.post(
            route('sendBackToSG'),
            {
                'requisitionId': requisitionData.id
            },
            {
                onSuccess: (page) => {
                    setDialogTitle('Requerimento enviado');
                    setDialogBody(<ActionSuccessful dialogText={'Enviado à SG com sucesso.'} />);
                    openDialog();
                },
                onError: (errors) => console.log(errors)
            }
        );
    }
    return (
        <Button
            key="send_back_to_sg"
            onClick={handleSubmit}
            startIcon={<SendIcon />}
            {...styles}
        >
            Devolver à SG
        </Button>
    );
};

export default buttonComponentList;

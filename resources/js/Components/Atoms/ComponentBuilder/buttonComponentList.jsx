import React from "react";
import { Button, Tooltip, DialogActions, DialogContent, DialogContentText, Alert } from "@mui/material";
import { useDialogContext } from '../../Context/useDialogContext';
import ListOfReviewers from "../../Dialogs/ReviewerPicker";
import ActionSuccessful from "../../Dialogs/ActionSuccessful";
import AddRoleDialog from "../../Dialogs/AddRoleDialog";
import RequisitionsPeriodDialog from "../../Dialogs/RequisitionsPeriodDialog";
import RequisitionResultDialog from "../../Dialogs/RequisitionResultDialog";
import { router } from "@inertiajs/react";
import axios from "axios";

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

function MainPageButton({children, ...props}){
    return (
        <Button
            variant="contained"
            {...props}
        >
            {children}
        </Button>
    );
};

function RequisitionDetailButton({children, ...props}){
    return (
        <Button
            disableRipple
            variant="text"
            color="black"
            {...props}
        >
            {children}
        </Button>
    );
};

function HeaderButton({children, ...props}){
    return (
        <Button
            variant="outlined"
            style={{
                color: 'white',
                borderColor: 'white'
            }}
            {...props}
        >
            {children}
        </Button>
    );
};

let buttonComponentList = {};

buttonComponentList.add_role = () => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();

    function handleClick() {
        setDialogTitle('Adicionar papel');
        setDialogBody(<AddRoleDialog />);
        openDialog();
    }

    return (
        <MainPageButton
            onClick={handleClick}
        >
            Adicionar um papel
        </MainPageButton>
    );
};

buttonComponentList.admin = () => (
    <MainPageButton
        href={route('admin')}
        startIcon={<AdminPanelSettingsIcon />}
    >
        Administrar Sistema
    </MainPageButton>
);

buttonComponentList.automatic_requisition = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('automaticDeferral'),
                { 'requisitionId': params.requisitionId },
                {
                    onSuccess: (resp) => {
                        console.log(resp);
                        closeDialog();
                        setDialogTitle('Deferimento automático realizado');
                        setDialogBody(<ActionSuccessful dialogText={'O deferimento automático foi realizado com sucesso.'} />)
                        router.get(route('list'));
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
                        Tem certeza de que quer realizar o deferimento automático?
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={submitAndReturnToList}>Sim</Button>
                    <Button onClick={closeDialog} sx={{ color: 'red' }}>Não</Button>
                </DialogActions>
            </>
        );
        openDialog();
    };

    return (
        <RequisitionDetailButton
            onClick={handleClick}
            startIcon={<PrecisionManufacturingIcon />}
        >
            Deferimento Automático
        </RequisitionDetailButton>
    );
};

buttonComponentList.edit_requisition = (params) => (
    <Tooltip
        title="Edição não permitida"
        disableHoverListener={params.requisitionEditionStatus || params.roleId != 1}
    >
        <span>
            <RequisitionDetailButton
                disabled={!params.requisitionEditionStatus && params.roleId == 1}
                href={route('updateRequisition.get', { 'requisitionId': params.requisitionId })}
                startIcon={<ModeEditIcon />}
            >
                Editar Requerimento
            </RequisitionDetailButton>
        </span>
    </Tooltip>
);

buttonComponentList.go_back = () => {
    return (
        <HeaderButton
            startIcon={<KeyboardReturnIcon />}
            onClick={() => window.history.back()}
        >
            Voltar
        </HeaderButton>
    );
}

buttonComponentList.new_requisition = (params) => (
    <Tooltip
        title="Disponível durante o período de requerimentos"
        disableHoverListener={params.requisitionCreationStatus || params.roleId == 2}
    >
        <MainPageButton
            disabled={!params.requisitionCreationStatus && params.roleId == 1}
            href={route('newRequisition.get')}
            startIcon={<AddIcon />}
        >
            Criar Requerimento
        </MainPageButton>
    </Tooltip>
);


buttonComponentList.export = () => (
    <MainPageButton
        href={route('exportRequisitionsGet')}
        startIcon={<FileDownloadIcon />}
    >
        Exportar
    </MainPageButton>
);


buttonComponentList.export_current = () => {
    const printDocument = () => {
        const input = document.getElementById('requisition-paper');
        html2canvas(input)
            .then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF();
                pdf.addImage(imgData, 'PNG', 0, 0, 260, 211);
                // pdf.output('dataurlnewwindow');
                pdf.save("download.pdf");
            });
    };

    return (
        <RequisitionDetailButton
            startIcon={<ReviewsIcon />}
            onClick={printDocument}
        >
            Exportar requerimento
        </RequisitionDetailButton>
    )
};

buttonComponentList.exit = () => {
    return (
        <HeaderButton
            href={route('logout')}
            startIcon={<LogoutIcon />}
        >
            Sair
        </HeaderButton>
    );
}

buttonComponentList.registered = (params) => {
    console.log(params);
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('registered'),
                { 'requisitionId': params.requisitionId },
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
                    <Button onClick={submitAndReturnToList}>Sim</Button>
                    <Button onClick={closeDialog} sx={{ color: 'red' }}>Não</Button>
                </DialogActions>
            </>
        );
        openDialog();
    };

    return (
        <RequisitionDetailButton
            onClick={handleClick}
            startIcon={<HowToRegIcon />}
        >
            Registrado no Jupiter
        </RequisitionDetailButton>
    )
};

buttonComponentList.requisition_history = (params) => {
    return (
        <RequisitionDetailButton
            href={route('record.requisition', { 'requisitionId': params.requisitionId })}
            startIcon={<HistoryIcon />}
        >
            Histórico do Requerimento
        </RequisitionDetailButton>
    );
}

buttonComponentList.requisition_period = () => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();

    function handleClick() {
        axios.get(route('admin.getRequisitionPeriodStatus'))
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
        <MainPageButton
            onClick={handleClick}
        >
            Período de requerimentos
        </MainPageButton>
    );
};

buttonComponentList.reviews = (params) => {
    return (
        <RequisitionDetailButton
            href={route('reviewer.reviews', { 'requisitionId': params.requisitionId })}
            startIcon={<ReviewsIcon />}
        >
            Pareceres dados
        </RequisitionDetailButton>
    );
};

buttonComponentList.save = (params) => {
    return (
        <MainPageButton
            href={route('record.requisition', { 'requisitionId': params.requisitionId })}
            startIcon={<SaveIcon />}
        >
            Salvar alterações
        </MainPageButton>
    );
}


buttonComponentList.send_to_department = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();

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
                'requisitionId': params.requisitionId
            },
            {
                onSuccess: (page) => {
                    console.log(page.props.data);
                    setDialogTitle('Requerimento enviado');
                    setDialogBody(<ActionSuccessful dialogText={'Enviado ao departamento com sucesso.'} />);
                    openDialog();
                },
                onError: (errors) => console.log(errors)
            }
        );
    }

    return (
        <RequisitionDetailButton
            onClick={handleSubmit}
            startIcon={<SendIcon />}
        >
            Enviar para o Departamento
        </RequisitionDetailButton>
    );
};

buttonComponentList.send_to_reviewers = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    const handleClick = () => {
        axios.get(route('reviewer.reviewerPick'))
            .then((response) => {
                setDialogTitle('Lista de pareceristas');
                setDialogBody(
                    <ListOfReviewers
                        requisitionId={params.requisitionId}
                        reviewers={response.data}
                        closeDialog={closeDialog}
                    />
                );
                openDialog();
            }
        );
    }

    return (
        <RequisitionDetailButton
            onClick={handleClick}
            startIcon={<SendToMobileIcon />}
        >
            Enviar para Pareceristas
        </RequisitionDetailButton>
    );
};

buttonComponentList.submit_review = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();

    const handleSubmit = () => {
        router.post(
            route('submitReview'),
            {
                'requisitionId': params.requisitionId
            },
            {
                onSuccess: () => {
                    setDialogTitle('Parecer enviado');
                    setDialogBody(<ActionSuccessful dialogText={'O parecer foi enviado com sucesso.'} />);
                    openDialog();
                },
                onError: (errors) => console.log(errors)
            }
        );
    }

    return (
        <RequisitionDetailButton
            onClick={handleSubmit}
            startIcon={<AssignmentReturnIcon />}
        >
            Enviar parecer
        </RequisitionDetailButton>
    )
};

buttonComponentList.result = (params) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Resultado');
        setDialogBody(<RequisitionResultDialog requisitionId={params.requisitionId} />);
        openDialog();
    };

    return (
        <RequisitionDetailButton
            onClick={handleClick}
            startIcon={<AssignmentTurnedInIcon />}
        >
            Dar resultado
        </RequisitionDetailButton>
    )
};

export default buttonComponentList;
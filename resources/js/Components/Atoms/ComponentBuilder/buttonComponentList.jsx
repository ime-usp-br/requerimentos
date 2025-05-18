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

buttonComponentList.add_role = ({ styles }) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();
    function handleClick() {
        setDialogTitle('Adicionar papel');
        setDialogBody(<AddRoleDialog />);
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

buttonComponentList.automatic_requisition = ({ styles, actionParams }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('automaticDeferral'),
                { 'requisitionId': actionParams.requisitionId },
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
                    <Button onClick={closeDialog} sx={{ color: 'red' }}>Não</Button>
                    <Button onClick={submitAndReturnToList}>Sim</Button>
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

buttonComponentList.edit_requisition = ({ actionsParams = {}, styles = {} }) => {
    return (
        <Tooltip
            title="Edição não permitida"
            disableHoverListener={actionsParams.requisitionEditionStatus || actionsParams.roleId != 1}
        >
            <Button
                key="edit_requisition"
                disabled={!actionsParams.requisitionEditionStatus && actionsParams.roleId == 1}
                href={route('updateRequisition.get', { 'requisitionId': actionsParams.requisitionId })}
                startIcon={<ModeEditIcon />}
                {...styles}
            >
                Editar Requerimento
            </Button>
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

buttonComponentList.new_requisition = ({ actionsParams = {}, styles = {} }) => (
    <Tooltip
        title="Disponível durante o período de requerimentos"
        disableHoverListener={actionsParams.requisitionCreationStatus || actionsParams.roleId == 2}
    >
        <Button
            key="new_requisition"
            disabled={!actionsParams.requisitionCreationStatus && actionsParams.roleId == 1}
            href={route('newRequisition.get')}
            startIcon={<AddIcon />}
            {...styles}
        >
            Criar Requerimento
        </Button>
    </Tooltip>
);

buttonComponentList.export = ({ styles = {} }) => (
    <Button
        key="export"
        href={route('exportRequisitionsGet')}
        startIcon={<FileDownloadIcon />}
        {...styles}
    >
        Exportar
    </Button>
);

buttonComponentList.export_current = ({ styles = {} }) => {
    const printDocument = () => {
        const input = document.getElementById('requisition-paper');
        html2canvas(input)
            .then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF();
                pdf.addImage(imgData, 'PNG', 0, 0, 260, 211);
                pdf.save("download.pdf");
            });
    };
    return (
        <Button
            key="export_current"
            startIcon={<ReviewsIcon />}
            onClick={printDocument}
            {...styles}
        >
            Exportar requerimento
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

buttonComponentList.registered = ({ actionsParams = {}, styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('registered'),
                { 'requisitionId': actionsParams.requisitionId },
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
                    <Button onClick={closeDialog} sx={{ color: 'red' }}>Não</Button>
                    <Button onClick={submitAndReturnToList}>Sim</Button>
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

buttonComponentList.requisition_history = ({ actionsParams = {}, styles = {} }) => (
    <Button
        key="requisition_history"
        href={route('record.requisition', { 'requisitionId': actionsParams.requisitionId })}
        startIcon={<HistoryIcon />}
        {...styles}
    >
        Histórico do Requerimento
    </Button>
);

buttonComponentList.requisition_period = ({ styles = {} }) => {
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
        <Button
            key="requisition_period"
            onClick={handleClick}
            {...styles}
        >
            Período de requerimentos
        </Button>
    );
};

buttonComponentList.reviews = ({ actionsParams = {}, styles = {} }) => (
    <Button
        key="reviews"
        href={route('reviewer.reviews', { 'requisitionId': actionsParams.requisitionId })}
        startIcon={<ReviewsIcon />}
        {...styles}
    >
        Pareceres dados
    </Button>
);

buttonComponentList.save = ({ actionsParams = {}, styles = {} }) => (
    <Button
        key="save"
        href={route('record.requisition', { 'requisitionId': actionsParams.requisitionId })}
        startIcon={<SaveIcon />}
        {...styles}
    >
        Salvar alterações
    </Button>
);

buttonComponentList.send_to_department = ({ actionsParams = {}, styles = {} }) => {
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
                'requisitionId': actionsParams.requisitionId
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

buttonComponentList.send_to_reviewers = ({ actionsParams = {}, styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const handleClick = () => {
        axios.get(route('reviewer.reviewerPick'))
            .then((response) => {
                setDialogTitle('Lista de pareceristas');
                setDialogBody(
                    <ListOfReviewers
                        requisitionId={actionsParams.requisitionId}
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

buttonComponentList.submit_review = ({ actionsParams = {}, styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();
    const handleSubmit = () => {
        router.post(
            route('submitReview'),
            {
                'requisitionId': actionsParams.requisitionId
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
        <Button
            key="submit_review"
            onClick={handleSubmit}
            startIcon={<AssignmentReturnIcon />}
            {...styles}
        >
            Enviar parecer
        </Button>
    )
};

buttonComponentList.result = ({ actionsParams = {}, styles = {} }) => {
    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Resultado');
        setDialogBody(<RequisitionResultDialog requisitionId={actionsParams.requisitionId} />);
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

export default buttonComponentList;
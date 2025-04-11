import React from "react";
import { Button, Tooltip, DialogActions, DialogContent, DialogContentText, Divider } from "@mui/material";
import { useDialogContext } from '../../Context/useDialogContext';
import ListOfReviewers from "../../Dialogs/ReviewerPicker";
import ActionSuccessful from "../../Dialogs/ActionSuccessful";
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

const buttonSx = {
    // padding: 0,
    // margin: 0,
    // width: '100%',
    // alignItems: 'left',
    // justifyContent: 'flex-start'
};

let buttonComponentList = {};

buttonComponentList.admin = () => (
    <Button 
        // disableRipple
        variant="raised"
        sx={buttonSx} 
        size="large"
        color="primary" 
        href={route('admin')}
        startIcon={<AdminPanelSettingsIcon />}
    >
        Administrar Sistema
    </Button>
);

buttonComponentList.new_requisition = (params) => (
    <Tooltip 
        title="Disponível durante o período de requerimentos"
        disableHoverListener={params.requisitionPeriodStatus || params.roleId == 2}
    >
        <span>
            <Button 
                disableRipple
                variant="raised"
                disabled={!params.requisitionPeriodStatus && params.roleId != 2}
                size="large"
                color="primary" 
                href={route('newRequisition.get')}
                sx={{ width: '100%', ...buttonSx }}
                startIcon={<AddIcon />}
            >
                Criar Requerimento
            </Button>
        </span>
    </Tooltip>
);

buttonComponentList.edit_requisition = (params) => (
    <Tooltip 
        title="Edição não permitida"
        disableHoverListener={params.requisitionEditStatus || params.roleId != 1}
    >
        <span>
            <Button 
                disableRipple
                variant="raised"
                disabled={!params.requisitionEditStatus && params.roleId == 1}
                size="large"
                color="primary" 
                href={route('updateRequisition.get', { 'requisitionId': params.requisitionId })}
                sx={{ width: '100%', ...buttonSx }}
                startIcon={<ModeEditIcon />}
            >
                Editar Requerimento
            </Button>
        </span>
    </Tooltip>
);

buttonComponentList.export = () => (
    <Button 
        disableRipple
        variant="raised"
        sx={buttonSx} 
        size="large"
        color="primary" 
        href={route('exportRequisitionsGet')}
        startIcon={<FileDownloadIcon />}
    >
        Exportar
    </Button>
);

buttonComponentList.send_to_department = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();

    const handleSubmit = () => {
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
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            onClick={handleSubmit}
            startIcon={<SendIcon />}
        >
            Enviar para o Departamento
        </Button>
    );
};

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
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary"
            startIcon={<ReviewsIcon />}
            onClick={printDocument}
            >
            Exportar requerimento
        </Button>
    )
};

buttonComponentList.reviews = (params) => {
    return (
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            href={route('reviewer.reviews', { 'requisitionId': params.requisitionId })}
            startIcon={<ReviewsIcon />}
            >
            Pareceres dados
        </Button>
    );
};

buttonComponentList.requisition_history = (params) => {
    return (
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            href={route('record.requisition', { 'requisitionId': params.requisitionId })}
            startIcon={<HistoryIcon />}
        >
            Histórico do Requerimento
        </Button>
    );
}

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
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            onClick={handleClick}
            startIcon={<SendToMobileIcon />}
        >
            Enviar para Pareceristas
        </Button>
    );
};

buttonComponentList.automatic_requisition = (params) => {
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    const handleClick = () => {
        setDialogTitle('Confirmação');
        const submitAndReturnToList = () => {
            router.post(
                route('automaticDeferral'), 
                { 'requisitionId': params.requisitionId  },
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
        <Button 
            disableRipple   
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="secondary" 
            onClick={handleClick}
            startIcon={<PrecisionManufacturingIcon />}
        >
            Deferimento Automático
        </Button>
    );
};

buttonComponentList.registered = (params) => {
    return (
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            href={route('registered', { 'requisitionId': params.requisitionId })}
            startIcon={<HowToRegIcon />}
        >
            Registrado no Jupiter
        </Button>
    )
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
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary"
            onClick={handleSubmit}
            startIcon={<AssignmentReturnIcon />}
        >
            Enviar parecer
        </Button>
    )
};

buttonComponentList.requisition_period = (params) => {
    return (
        <Button 
            variant="contained" 
            color="primary" 
            onClick={params.handleOpenRequisitionPeriod}
        >
            Período de requerimentos
        </Button>
    );
};

buttonComponentList.add_role = (params) => {
    return (
        <Button 
            variant="contained" 
            color="primary" 
            onClick={params.handleOpenAddRole}
        >
            Adicionar um papel
        </Button>
    );
};

buttonComponentList.save = (params) => {
    return (
        <Button 
            disableRipple
            variant="raised"
            sx={buttonSx} 
            size="large"
            color="primary" 
            href={route('record.requisition', { 'requisitionId': params.requisitionId })}
            startIcon={<SaveIcon />}
        >
            Salvar alterações
        </Button>
    );
}

buttonComponentList.go_back = () => {
    return (
        <Button 
            variant="outlined"
            size="large"
            style={{
                color: 'white',
                borderColor: 'white'
            }}
            startIcon={<KeyboardReturnIcon />}
            onClick={() => window.history.back()}
        >
            Voltar
        </Button>
    );
}

buttonComponentList.exit = () => {
    return (
        <Button 
            variant="outlined" 
            size="large"
            style={{
                color: 'white',
                borderColor: 'white'
            }}
            href={route('logout')}
            startIcon={<LogoutIcon />}
        >
            Sair
        </Button>
    );
}

console.log(buttonComponentList);

export default buttonComponentList;
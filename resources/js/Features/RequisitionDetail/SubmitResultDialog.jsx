import React, { useState } from "react";
import {
	DialogContent,
	DialogActions,
	Button,
	TextField,
	Radio,
	RadioGroup,
	FormControlLabel,
	FormControl,
	Stack,
    Alert
} from "@mui/material";
import { router } from "@inertiajs/react";
import { useDialogContext } from '../../Context/useDialogContext';
import ActionSuccessful from "../../Dialogs/ActionSuccessful";

function SubmitResultDialog({ requisitionId, type = 'requisition', submitRoute = 'giveResultToRequisition' }) {
	const [selectedOption, setSelectedOption] = useState("");
	const [comment, setComment] = useState("");
    const [alert, setAlert] = useState(false);
	const [alertText, setAlertText] = useState("");
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    
	const handleOptionChange = (event) => {
		setSelectedOption(event.target.value);
	};

	const handleCommentChange = (event) => {
		setComment(event.target.value);
	};

	const handleSubmit = (event) => {
		event.preventDefault();

		console.log(selectedOption, comment);
        
		if (selectedOption == "") {
			setAlertText("Não submeta um resultado antes de escolher uma opção.");
            setAlert(true);
            return;
		}
        if (selectedOption == "Indeferido" && comment.trim() == "") {
			setAlertText("Uma justificativa é necessária para indeferir.");
            setAlert(true);
            return;
        }

        router.post(
            route(submitRoute),
            { 
				'requisitionId': requisitionId,
				'result': selectedOption,
				'result_text': comment == "" && "Deferido"
			},
            {
                onSuccess: (resp) => {
                    console.log(resp);
                    closeDialog();
                    setDialogTitle('Enviado com sucesso');
                    setDialogBody(<ActionSuccessful dialogText={'O resultado foi enviado com sucesso.'} />)
                    openDialog();
                },
                onError: (error) => {
                    console.log(error);
                    closeDialog();
                }
            }
        );
	};

	return (
		<>
			<DialogContent>
				<form id="result-form" onSubmit={handleSubmit} autoComplete="off">
					<Stack spacing={2}>
						<FormControl component="fieldset">
							<RadioGroup name="resultOption" value={selectedOption} onChange={handleOptionChange}>
								{ (type == 'requisition') &&
									<FormControlLabel value="Inconsistência nas informações" control={<Radio />} label="Inconsistência nas informações" />
								}
								<FormControlLabel value="Deferido" control={<Radio />} label="Deferido" />
								<FormControlLabel value="Indeferido" control={<Radio />} label="Indeferido" />
							</RadioGroup>
						</FormControl>
						<TextField
							label="Observações"
							variant="outlined"
							fullWidth
							multiline
							rows={4}
							value={comment}
							onChange={handleCommentChange}
						/>
					</Stack>
				</form>
			</DialogContent>
            { alert && <Alert severity="error">{alertText}</Alert> }
			<DialogActions>
                <Button color="error" onClick={closeDialog}>
                    Cancelar
                </Button>
				<Button variant="contained" type="submit" form="result-form">
					Confirmar
				</Button>
			</DialogActions>
		</>
	);
};

export default SubmitResultDialog;
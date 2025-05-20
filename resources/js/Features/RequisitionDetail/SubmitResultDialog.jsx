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
	Alert,
	Typography,
	IconButton
} from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";
import { useForm } from "@inertiajs/react";
import { useDialogContext } from '../../Context/useDialogContext';
import ActionSuccessful from "../../Dialogs/ActionSuccessful";

function SubmitResultDialog({ requisitionId, type = 'requisition', submitRoute = 'giveResultToRequisition' }) {
	const [alertText, setAlertText] = useState("");
	const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
	
	const { data, setData, post, processing, errors } = useForm({
		requisitionId: requisitionId,
		result: "",
		result_text: ""
	});

	const handleSubmit = (event) => {
		event.preventDefault();

		// Let the backend handle validation through Inertia's useForm
		post(route(submitRoute), {
			onSuccess: (resp) => {
				closeDialog();
				setDialogTitle('Enviado com sucesso');
				setDialogBody(<ActionSuccessful dialogText={'O resultado foi enviado com sucesso.'} />);
				openDialog();
			},
			onError: (errors) => {
				console.log(errors);
				// Errors are now automatically handled by useForm
				// But we can still set an alert for custom error messages
				if (typeof errors === 'string') {
					setAlertText(errors);
				}
			}
		});
	};

	return (
		<>
			<DialogContent>
				<form id="result-form" onSubmit={handleSubmit} autoComplete="off">
					<Stack spacing={2}>
						<FormControl component="fieldset" error={errors.result ? true : false}>
							<RadioGroup
								name="result"
								value={data.result}
								onChange={(e) => setData('result', e.target.value)}
							>
								{type === 'requisition' &&
									<FormControlLabel value="Inconsistência nas informações" control={<Radio />} label="Inconsistência nas informações" />
								}
								<FormControlLabel value="Deferido" control={<Radio />} label="Deferido" />
								<FormControlLabel value="Indeferido" control={<Radio />} label="Indeferido" />
							</RadioGroup>
							{errors.result && (
								<Typography
									variant="caption"
									color="error"
									sx={{ marginTop: '3px', marginLeft: '14px' }}
								>
									{errors.result}
								</Typography>
							)}
						</FormControl>
						<TextField
							label="Observações"
							variant="outlined"
							fullWidth
							multiline
							rows={4}
							name="result_text"
							value={data.result_text}
							onChange={(e) => setData('result_text', e.target.value)}
							error={errors.result_text ? true : false}
							helperText={errors.result_text}
						/>
					</Stack>
				</form>
			</DialogContent>
			{alertText && (
				<Alert
					severity="error"
					action={
						<IconButton
							aria-label="close"
							color="inherit"
							size="small"
							onClick={() => setAlertText("")}
						>
							<CloseIcon fontSize="inherit" />
						</IconButton>
					}
				>
					{alertText}
				</Alert>
			)}
			<DialogActions>
				<Button color="error" onClick={closeDialog}>
					Cancelar
				</Button>
				<Button 
					variant="contained" 
					type="submit" 
					form="result-form" 
					disabled={processing ||	!data.result }
				>
					Confirmar
				</Button>
			</DialogActions>
		</>
	);
}

export default SubmitResultDialog;

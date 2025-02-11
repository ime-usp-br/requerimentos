import React from "react";
import {
	Button,
	Dialog,
	DialogActions,
	DialogContent,
	DialogTitle,
	Switch,
	FormControlLabel,
	FormControl,
	Stack,
	Collapse
} from "@mui/material";
import { useForm } from "@inertiajs/react";

const RequisitionsPeriodDialog = ({ requisitionSubmissionIsOpen, requisitionEditionIsOpen, open, handleClose }) => {
	const { data, setData, post, processing } = useForm({
		requisitionSubmissionIsOpen: requisitionSubmissionIsOpen,
		requisitionEditionIsOpen: requisitionEditionIsOpen
	});

	const handleChange = (event) => {
		setData(event.target.name, event.target.checked);
	};

	const handleSubmit = (event) => {
		event.preventDefault();
		post(route('requisitions.period.update'), {
			onSuccess: () => handleClose()
		});
	};

	return (
		<Dialog
			open={open}
			onClose={handleClose}
			aria-labelledby="alert-dialog-title"
			aria-describedby="alert-dialog-description"
		>
			<DialogTitle id="alert-dialog-title">
				Configuração do período de Requerimentos
			</DialogTitle>
			<DialogContent>
				<FormControl component="fieldset">
					<Stack spacing={2}>
						<FormControlLabel
							control={
								<Switch
									checked={data.requisitionSubmissionIsOpen}
									onChange={handleChange}
									name="requisitionSubmissionIsOpen"
									color="primary"
								/>
							}
							label="Permitir submissão de novos requerimentos"
						/>
						<Collapse in={!data.requisitionSubmissionIsOpen}>
							<FormControlLabel
								control={
									<Switch
										checked={data.requisitionEditionIsOpen}
										onChange={handleChange}
										name="requisitionEditionIsOpen"
										color="primary"
									/>
								}
								label="Permitir edição de requerimentos submetidos"
							/>
						</Collapse>
					</Stack>
				</FormControl>
			</DialogContent>
			<DialogActions>
				<Button onClick={handleClose} color="error">
					Cancelar
				</Button>
				<Button onClick={handleSubmit} color="primary" variant="contained" disabled={processing}>
					Salvar
				</Button>
			</DialogActions>
		</Dialog>
	);
};

export default RequisitionsPeriodDialog;

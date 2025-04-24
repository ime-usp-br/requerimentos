import React, { useState } from "react";
import {
	Button,
	DialogActions,
	DialogContent,
	Switch,
	FormControlLabel,
	FormControl,
	Stack,
	Collapse
} from "@mui/material";
import axios from "axios";
import { useDialogContext } from '../Context/useDialogContext';


const RequisitionsPeriodDialog = ({ isCreationEnabled, isUpdateEnabled }) => {
    const { closeDialog } = useDialogContext();

	const [data, setData] = useState({
		isCreationEnabled: isCreationEnabled,
		isUpdateEnabled: isUpdateEnabled
	});

	const handleChange = (event) => {
		setData((prevData) => {
			const updatedData = { ...prevData, [event.target.name]: event.target.checked };
			if (event.target.name === "isCreationEnabled" && event.target.checked) {
				updatedData.isUpdateEnabled = true;
			}
			return updatedData;
		});
	};
	const handleSubmit = async (event) => {
		event.preventDefault();
		try {
			await axios.post(route('admin.setRequisitionPeriodStatus'), data);
			closeDialog();
		} catch (error) {
			console.error("Error updating requisitions period:", error);
		}
	};

	return (
		<>
			<DialogContent>
				<FormControl component="fieldset">
					<Stack spacing={2} alignItems="flex-start">
						<FormControlLabel
							control={
								<Switch
									checked={data.isCreationEnabled}
									onChange={handleChange}
									name="isCreationEnabled"
									color="primary"
								/>
							}
							label="Permitir submissão de novos requerimentos"
						/>
						<FormControlLabel
							control={
								<Switch
									checked={data.isUpdateEnabled}
									onChange={handleChange}
									name="isUpdateEnabled"
									color="primary"
									disabled={data.isCreationEnabled}
								/>
							}
							label="Permitir edição de requerimentos submetidos"
						/>
					</Stack>
				</FormControl>
			</DialogContent>
			<DialogActions>
				<Button onClick={closeDialog} color="error">
					Cancelar
				</Button>
				<Button onClick={handleSubmit} color="primary" variant="contained">
					Salvar
				</Button>
			</DialogActions>
		</>
	);
};

export default RequisitionsPeriodDialog;

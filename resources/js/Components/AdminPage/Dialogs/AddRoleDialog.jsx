import React from "react";
import {
	Button,
	Dialog,
	DialogActions,
	DialogContent,
	DialogTitle,
	TextField,
	Radio,
	RadioGroup,
	FormControlLabel,
	FormControl,
	FormLabel,
	Stack,
	Collapse
} from "@mui/material";
import { useForm } from "@inertiajs/react";

const AddRoleDialog = ({ open, handleClose }) => {
	const { data, setData, post, processing, errors } = useForm({
		nusp: '',
		role: '',
		department: ''
	});

	const handleChange = (event) => {
		setData(event.target.name, event.target.value);
	};

	const handleSubmit = (event) => {
		event.preventDefault();
		post(route('role.add'), {
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
				Adicione um papel
			</DialogTitle>
			<DialogContent>
				<form id="role-form" onSubmit={handleSubmit} autoComplete="off">
					<Stack>
						<TextField
							margin="dense"
							id="nusp"
							label="Número USP do usuário"
							type="text"
							variant="outlined"
							name="nusp"
							value={data.nusp}
							onChange={handleChange}
							error={!!errors.nusp}
							helperText={errors.nusp}
							required
						/>
						<FormControl component="fieldset" margin="dense" required>
							<FormLabel component="legend">Tipo de papel</FormLabel>
							<RadioGroup
								aria-label="role"
								name="role"
								value={data.role}
								onChange={handleChange}
							>
								<FormControlLabel value="Parecerista" control={<Radio />} label="Parecerista" />
								<FormControlLabel value="Serviço de Graduação" control={<Radio />} label="Serviço de Graduação" />
								<FormControlLabel value="Secretaria" control={<Radio />} label="Secretaria de Departamento" />
							</RadioGroup>
						</FormControl>
						<Collapse in={data.role === 'Secretaria'}>
							<FormControl component="fieldset" margin="dense" required={data.role === 'department'}>
								<FormLabel component="legend">Departamento</FormLabel>
								<RadioGroup
									aria-label="department"
									name="department"
									value={data.department}
									onChange={handleChange}
								>
									<FormControlLabel value="MAC" control={<Radio />} label="MAC" />
									<FormControlLabel value="MAP" control={<Radio />} label="MAP" />
									<FormControlLabel value="MAT" control={<Radio />} label="MAT" />
									<FormControlLabel value="MAE" control={<Radio />} label="MAE" />
									<FormControlLabel value="VRT" control={<Radio />} label="Virtual" />
								</RadioGroup>
							</FormControl>
						</Collapse>
					</Stack>
				</form>
			</DialogContent>
			<DialogActions>
				<Button color="error" onClick={handleClose}>
					Cancelar
				</Button>
				<Button variant="contained" type="submit" form="role-form" disabled={processing}>
					Adicionar
				</Button>
			</DialogActions>
		</Dialog>
	);
};

export default AddRoleDialog;

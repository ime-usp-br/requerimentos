import React, { useEffect, useState } from "react";
import {
	Button,
	DialogActions,
	DialogContent,
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
import { useDialogContext } from '../../Context/useDialogContext';

function AddRoleDialog({ user }) {
	const { closeDialog } = useDialogContext();
	const [roles, setRoles] = useState([]);
	const [departments, setDepartments] = useState([]);
	const { data, setData, post, processing, errors } = useForm({
		nusp: '',
		roleId: '',
		departmentId: user.currentDepartmentId || '',
	});
	const selectedRole = roles.find(r => String(r.id) === String(data.roleId));

	useEffect(() => {
		fetch(route('role.listRolesAndDepartments'))
			.then(res => res.json())
			.then(({ roles, departments }) => {
				setRoles(roles);
				setDepartments(departments);
			});
	}, []);

	const handleChange = (event) => {
		const { name, value } = event.target;
		setData(name, value);
		if (name === "roleId") {
			const role = roles.find(r => String(r.id) === String(value));
			if (!role?.has_department) {
				setData("departmentId", "");
			}
		}
	};

	const handleSubmit = (event) => {
		event.preventDefault();
		post(route('role.add'), {
			onSuccess: () => {
				closeDialog();
				window.location.reload();
			}
		});
	};

	return (
		<>
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
								name="roleId"
								value={data.roleId}
								onChange={handleChange}
							>
								{roles.map(role => (
									<FormControlLabel
										key={role.id}
										value={String(role.id)}
										control={<Radio />}
										label={role.name}
									/>
								))}
							</RadioGroup>
						</FormControl>
						{departments && (
							<Collapse in={!!selectedRole && selectedRole.has_department}>
								<FormControl component="fieldset" margin="dense" required={!!selectedRole && selectedRole.has_department}>
									<FormLabel component="legend">Departamento</FormLabel>
									<RadioGroup
										aria-label="department"
										name="departmentId"
										value={data.departmentId}
										onChange={handleChange}
									>
										{departments.map(dep => (
											<FormControlLabel
												key={dep.id}
												value={String(dep.id)}
												control={<Radio />}
												label={dep.name}
											/>
										))}
									</RadioGroup>
								</FormControl>
							</Collapse>
						)}
					</Stack>
				</form>
			</DialogContent>
			<DialogActions>
				<Button color="error" onClick={closeDialog}>
					Cancelar
				</Button>
				<Button variant="contained" type="submit" form="role-form" disabled={processing}>
					Adicionar
				</Button>
			</DialogActions>
		</>
	);
};

export default AddRoleDialog;

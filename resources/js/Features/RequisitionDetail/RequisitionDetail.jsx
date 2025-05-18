import React from 'react';
import {
	Paper, Typography, Stack, Divider, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, useTheme, useMediaQuery,
} from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Link from '@mui/material/Link';

const RequisitionDetail = ({
	requisition,
	takenDiscs,
	documents }) => {

	const theme = useTheme();
	const isMedium = useMediaQuery(theme.breakpoints.up('md'));

	const getElevation = () => {
		if (isMedium) return 3;
		return 0;
	};

	let resultColor;
	switch (requisition.result) {
		case "Deferido":
			resultColor = "green";
			break;
		case "Indeferido":
		case "Inconsistência nas informações":
			resultColor = "red";
			break;
		default:
			resultColor = "";
	}

	return (
		<Stack
			direction='row'
			spacing={10}
			sx={{
				justifyContent: 'center',
				alignItems: 'top',
				width: '100%',
				paddingTop: 4
			}}>

			<Paper
				id="requisition-paper"
				elevation={getElevation()}
				sx={{
					padding: { md: 2 },
					width: '65%'
				}}
			>
				<Stack spacing={3} divider={<Divider orientation="horizontal" flexItem />}>
					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Dados Pessoais</Typography>
						<Typography variant="body2"><strong>Nome:</strong> {requisition.student_name}</Typography>
						<Typography variant="body2"><strong>Email:</strong> {requisition.email}</Typography>
						<Typography variant="body2"><strong>Número USP:</strong> {requisition.student_nusp}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Curso Atual</Typography>
						<Typography variant="body2">{requisition.course}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Disciplina Requerida</Typography>
						<Typography variant="body2"><strong>Código:</strong> {requisition.requested_disc_code}</Typography>
						<Typography variant="body2"><strong>Nome:</strong> {requisition.requested_disc}</Typography>
						<Typography variant="body2"><strong>Tipo:</strong> {requisition.requested_disc_type}</Typography>
						<Typography variant="body2"><strong>Departamento:</strong> {requisition.department}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Disciplinas Cursadas</Typography>
						<TableContainer component={Paper}>
							<Table>
								<TableHead>
									<TableRow>
										<TableCell>Nome</TableCell>
										<TableCell>Instituição</TableCell>
										<TableCell>Código</TableCell>
										<TableCell>Ano</TableCell>
										<TableCell>Nota</TableCell>
										<TableCell>Semestre</TableCell>
									</TableRow>
								</TableHead>
								<TableBody>
									{takenDiscs.map((disc, index) => (
										<TableRow key={index}>
											<TableCell>{disc.name}</TableCell>
											<TableCell>{disc.institution}</TableCell>
											<TableCell>{disc.code}</TableCell>
											<TableCell>{disc.year}</TableCell>
											<TableCell>{disc.grade}</TableCell>
											<TableCell>{disc.semester}</TableCell>
										</TableRow>
									))}
								</TableBody>
							</Table>
						</TableContainer>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Documentos</Typography>
						{documents && documents.length > 0 && documents.map((doc) => (
							<Typography variant="body2" key={doc.id}>
								<Link href={`/documents/${doc.id}/view`} target="_blank" rel="noopener" sx={{ display: 'inline-flex', alignItems: 'center' }} underline="hover">
									{doc.type} <OpenInNewIcon fontSize="small" sx={{ ml: 0.5 }} />
								</Link>
							</Typography>
						))}
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Observações</Typography>
						<Typography variant="body2">{requisition.observations}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Resultado</Typography>
						<Typography variant="body2" color={resultColor}><strong>Status:</strong> {requisition.result}</Typography>
						<Typography variant="body2"><strong>Comentário:</strong> {requisition.result_text}</Typography>
					</Stack>
				</Stack>
			</Paper>
		</Stack>
	);
};

export default RequisitionDetail;
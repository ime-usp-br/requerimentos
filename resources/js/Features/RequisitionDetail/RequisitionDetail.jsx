import React from 'react';
import {
	Paper, Typography, Stack, Grid2
} from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Link from '@mui/material/Link';
import { useRequisitionContext } from './useRequisitionContext';

const formatDate = (originalDate) => {
	const date = new Date(originalDate);
	const pad = (n) => n.toString().padStart(2, '0');
	return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const RequisitionData = ({ requisitionData }) => (
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
			sx={{
				backgroundColor: '#BEFFDA'
			}}
		>
			<Typography variant='h5'><strong>Requerimento {requisitionData.id}</strong></Typography>
		</Grid2>
		<Grid2 size={1} />
		<Grid2 size={1}>
			<Typography variant='body1'><strong>Situação</strong></Typography>
		</Grid2>
		<Grid2 size={5}>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>Abertura:</strong></Typography>
		</Grid2>
		<Grid2 size={4}>
			<Typography variant='body1'>{formatDate(requisitionData.created_at)}</Typography>
		</Grid2>

		<Grid2 size={1} />
		<Grid2 size={6}>
			<Typography variant='body1'>{requisitionData.situation}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>Últ. Mod.:</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body1'>{formatDate(requisitionData.updated_at)}</Typography>
		</Grid2>

		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#E3FAFF'
			}}
		>
			<Grid2 size={2}>
				<Typography variant='body1' align='right'><strong>Resultado:</strong></Typography>
			</Grid2>
			<Grid2 size={10}>
				<Typography variant='body1'>{requisitionData.result}</Typography>
			</Grid2>
			<Grid2 size={2}>
				<Typography variant='body1' align='right'><strong>Justificativa:</strong></Typography>
			</Grid2>
			<Grid2 size={10}>
				<Typography variant='body1'>{requisitionData.result_text}</Typography>
			</Grid2>
		</Grid2>
	</Grid2>
);

const StudentData = ({ requisitionData }) => (
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
			sx={{
				backgroundColor: '#BEFFDA'
			}}
		>
			<Typography variant='h5'><strong>Dados Estudantis</strong></Typography>
		</Grid2>
		<Grid2 size={1} />
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>Nome:</strong></Typography>
		</Grid2>
		<Grid2 size={5}>
			<Typography variant='body1'>{requisitionData.student_name}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>NUSP:</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body1'>{requisitionData.student_nusp}</Typography>
		</Grid2>
		<Grid2 size={2} />

		<Grid2 size={1} />
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>Curso:</strong></Typography>
		</Grid2>
		<Grid2 size={5}>
			<Typography variant='body1'>{requisitionData.course}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body1' align='right'><strong>email:</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body1'>{requisitionData.email}</Typography>
		</Grid2>
		<Grid2 size={2} />

		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#E3FAFF'
			}}
		>
			<Grid2 size={1} />
			<Grid2 size={11}>
				<Typography variant='body1'><strong>Observações do pedido</strong></Typography>
			</Grid2>
			<Grid2 size={1} />
			<Grid2 size={11}>
				<Typography variant='body1'>{requisitionData.observations}</Typography>
			</Grid2>
		</Grid2>
	</Grid2>
);

const DocumentLink = ({ title, doc }) => (
	<Typography variant="body1" key={doc.id} align='right'>
		<Link href={`/documents/${doc.id}/view`} target="_blank" rel="noopener" sx={{ display: 'inline-flex', alignItems: 'center' }} underline="hover">
			{title} <OpenInNewIcon fontSize="small" sx={{ ml: 0.5 }} />
		</Link>
	</Typography>
);

const DisciplinesData = ({ requisitionData, takenDiscs, documents }) => (
	<Grid2 container rowSpacing={1} columnSpacing={2}>
		<Grid2
			size={12}
			sx={{
				backgroundColor: '#BEFFDA'
			}}
		>
			<Typography variant='h5'><strong>Disciplinas</strong></Typography>
		</Grid2>
		<Grid2 size={8}>
			<Typography variant='body1'><strong>Requerida</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<DocumentLink title={'Ementa'} doc={documents[0]} />
		</Grid2>
		<Grid2 size={2}>
			<DocumentLink title={'Histórico'} doc={documents[2]} />
		</Grid2>

		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#F6A828'
			}}
		>
			<Grid2 size={1} />
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Código</strong></Typography>
			</Grid2>
			<Grid2 size={4}>
				<Typography variant='body1'><strong>Nome</strong></Typography>
			</Grid2>
			<Grid2 size={3}>
				<Typography variant='body1'><strong>Departamento</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Tipo</strong></Typography>
			</Grid2>
			<Grid2 size={2} />
		</Grid2>

		<Grid2 size={2}><Typography variant='body1' align='right'>{'MAC0422' || requisitionData.requested_disc_code}</Typography></Grid2>
		<Grid2 size={4}>
			<Typography variant='body1'>{'Algoritmos e Estruturas de Dados para Engenharia Elétrica' || requisitionData.requested_disc}</Typography>
		</Grid2>
		<Grid2 size={3}>
			<Typography variant='body1'>{'Disciplina de fora do IME' || requisitionData.requested_disc}</Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body1'>{'Optativa Eletiva' || requisitionData.requested_disc}</Typography>
		</Grid2>

		<Grid2 size={8}>
			<Typography variant='body1'><strong>Cursadas</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<DocumentLink title={'Ementa(s)'} doc={documents[1]} />
		</Grid2>
		<Grid2 size={2}>
			<DocumentLink title={'Histórico'} doc={documents[3]} />
		</Grid2>

		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#F6A828'
			}}
		>
			<Grid2 size={1} />
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Código</strong></Typography>
			</Grid2>
			<Grid2 size={4}>
				<Typography variant='body1'><strong>Nome</strong></Typography>
			</Grid2>
			<Grid2 size={3}>
				<Typography variant='body1'><strong>Instituição</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Nota</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Ano</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body1'><strong>Semestre</strong></Typography>
			</Grid2>
		</Grid2>
		{takenDiscs.map((disc, _) => (
			<>
				<Grid2 size={2}><Typography variant='body1' align='right'>{disc.code}</Typography></Grid2>
				<Grid2 size={4}><Typography variant='body1'>{disc.name}</Typography></Grid2>
				<Grid2 size={3}><Typography variant='body1'>{disc.institution}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body1'>{disc.grade}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body1'>{disc.year}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body1'>{disc.semester}</Typography></Grid2>
			</>
		))}
	</Grid2>
);

const Reviews = ({ requisitionData }) => <>{
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
			sx={{
				backgroundColor: '#FDFFBE'
			}}
		>
			<Typography variant='h5'><strong>Pareceres</strong></Typography>
		</Grid2>
		{requisitionData.reviews.map((review) => (
			<>
				<Grid2 size={1} />
				<Grid2 size={1}>
					<Typography variant='body1' align='right'><strong>Nome:</strong></Typography>
				</Grid2>
				<Grid2 size={5}>
					<Typography variant='body1'>{review.reviewer_name}</Typography>
				</Grid2>
				<Grid2 size={1}>
					<Typography variant='body1' align='right'><strong>NUSP:</strong></Typography>
				</Grid2>
				<Grid2 size={2}>
					<Typography variant='body1'>{review.reviewer_nusp}</Typography>
				</Grid2>
				<Grid2 size={2} />

				<Grid2 size={1} />
				<Grid2 size={1}>
					<Typography variant='body1' align='right'><strong>Decisão:</strong></Typography>
				</Grid2>
				<Grid2 size={4}>
					<Typography variant='body1'>{review.reviewer_decision}</Typography>
				</Grid2>
				<Grid2 size={2}>
					<Typography variant='body1' align='right'><strong>Última Mod.:</strong></Typography>
				</Grid2>
				<Grid2 size={2}>
					<Typography variant='body1'>{formatDate(review.updated_at)}</Typography>
				</Grid2>
				<Grid2 size={2} />
		
				<Grid2
					container
					size={12} 
					columnSpacing={1.5}
					sx={{
						backgroundColor: '#E3FAFF'
					}}
				>
					<Grid2 size={2}>
						<Typography variant='body1' align='right'><strong>Justificativa:</strong></Typography>
					</Grid2>
					<Grid2 size={10}>
						<Typography variant='body1'>{requisitionData.observations}</Typography>
					</Grid2>
				</Grid2>
			</>
		))}
	</Grid2>
}</>;

const RequisitionDetail = ({
	takenDiscs,
	documents
}) => {
	const { requisitionData } = useRequisitionContext();

	console.log(requisitionData);

	let resultColor;
	switch (requisitionData.result) {
		case "Deferido":
			resultColor = "green";
			break;
		case "Indeferido":
			resultColor = "red";
			break;
		case "Inconsistência nas informações":
			resultColor = "orange";
			break;
		case "Cancelado":
			resultColor = "grey";
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
				width: '1440px'
			}}>

			<Paper
				id="requisition-paper"
				elevation={0}
				sx={{
					padding: 1,
					width: '100%'
				}}
			>
				<Grid2
					container
					rowSpacing={1}
					display='grid'
				>
					<RequisitionData requisitionData={requisitionData} />
					{/* <Divider /> */}
					<StudentData requisitionData={requisitionData} />
					{/* <Divider /> */}
					<DisciplinesData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />

					<Reviews requisitionData={requisitionData} />
				</Grid2>
				{/* <Divider />
				<Stack spacing={3} divider={<Divider orientation="horizontal" flexItem />}>
					<Stack spacing={1}>
						<Typography variant="body2">ID do requerimento: {requisitionData.id}</Typography>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Dados Pessoais</Typography>
						<Typography variant="body2"><strong>Nome:</strong> {requisitionData.student_name}</Typography>
						<Typography variant="body2"><strong>Email:</strong> {requisitionData.email}</Typography>
						<Typography variant="body2"><strong>Número USP:</strong> {requisitionData.student_nusp}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Curso Atual</Typography>
						<Typography variant="body2">{requisitionData.course}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Disciplina Requerida</Typography>
						<Typography variant="body2"><strong>Código:</strong> {requisitionData.requested_disc_code}</Typography>
						<Typography variant="body2"><strong>Nome:</strong> {requisitionData.requested_disc}</Typography>
						<Typography variant="body2"><strong>Tipo:</strong> {requisitionData.requested_disc_type}</Typography>
						<Typography variant="body2"><strong>Departamento:</strong> {requisitionData.department}</Typography>
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
						<Typography variant="body2">{requisitionData.observations}</Typography>
					</Stack>

					<Stack spacing={1}>
						<Typography variant="h6" sx={{ fontWeight: 600 }}>Resultado</Typography>
						<Typography variant="body2" color={resultColor}><strong>Status:</strong> {requisitionData.result}</Typography>
						<Typography variant="body2"><strong>Justificativa:</strong> {requisitionData.result_text}</Typography>
					</Stack>
				</Stack> */}
			</Paper>
		</Stack>
	);
};

export default RequisitionDetail;
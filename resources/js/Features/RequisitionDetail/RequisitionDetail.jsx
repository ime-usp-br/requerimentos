import React from 'react';
import {
	Paper, Typography, Stack, Grid2, Divider, TableContainer, Table, TableHead, TableRow, TableCell, TableBody
} from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Link from '@mui/material/Link';
import { useRequisitionContext } from './useRequisitionContext';
import { useUser } from '../../Context/useUserContext';

const formatDate = (originalDate) => {
	const date = new Date(originalDate);
	const pad = (n) => n.toString().padStart(2, '0');
	return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const RequisitionData = ({ requisitionData }) => (
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
			// sx={{
			// 	backgroundColor: '#BEFFDA'
			// }}
		>
			<Typography variant='h6'><strong>Requerimento {requisitionData.id}</strong></Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body2'><strong>Situação:</strong></Typography>
		</Grid2>
		<Grid2 size={6}>
			<Typography variant='body2'>{requisitionData.situation}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body2'><strong>Abertura:</strong></Typography>
		</Grid2>
		<Grid2 size={4}>
			<Typography variant='body2'>{formatDate(requisitionData.created_at)}</Typography>
		</Grid2>


		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#E3FAFF'
			}}
		>
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Resultado:</strong></Typography>
			</Grid2>
			<Grid2 size={6}>
				<Typography variant='body2'>{requisitionData.result}</Typography>
			</Grid2>

			<Grid2 size={1}>
				<Typography variant='body2'><strong>Últ. Mod.:</strong></Typography>
			</Grid2>
			<Grid2 size={4}>
				<Typography variant='body2'>{formatDate(requisitionData.updated_at)}</Typography>
			</Grid2>
			<Grid2 size={12}>
				<Typography variant='body2'><strong>Justificativa</strong></Typography>
			</Grid2>
			<Grid2 size={12}>
				<Typography variant='body2'>{requisitionData.result_text}</Typography>
			</Grid2>
		</Grid2>
	</Grid2>
);

const StudentData = ({ requisitionData }) => (
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
			// sx={{
			// 	backgroundColor: '#BEFFDA'
			// }}
		>
			<Typography variant='h6'><strong>Dados Estudantis</strong></Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body2'><strong>Nome:</strong></Typography>
		</Grid2>
		<Grid2 size={6}>
			<Typography variant='body2'>{requisitionData.student_name}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body2'><strong>NUSP:</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body2'>{requisitionData.student_nusp}</Typography>
		</Grid2>
		<Grid2 size={2} />

		<Grid2 size={1}>
			<Typography variant='body2'><strong>Curso:</strong></Typography>
		</Grid2>
		<Grid2 size={6}>
			<Typography variant='body2'>{requisitionData.course}</Typography>
		</Grid2>
		<Grid2 size={1}>
			<Typography variant='body2'><strong>email:</strong></Typography>
		</Grid2>
		<Grid2 size={2}>
			<Typography variant='body2'>{requisitionData.email}</Typography>
		</Grid2>
		<Grid2 size={2} />

		<Grid2
			container
			size={12}
			sx={{
				backgroundColor: '#E3FAFF'
			}}
		>
			<Grid2 size={12}>
				<Typography variant='body2'><strong>Observações do pedido</strong></Typography>
			</Grid2>
			<Grid2 size={12}>
				<Typography variant='body2'>{requisitionData.observations}</Typography>
			</Grid2>
		</Grid2>
	</Grid2>
);

const DocumentLink = ({ title, doc }) => (
	<Typography variant="body2" key={doc.id} align='right'>
		<Link href={`/documents/${doc.id}/view`} target="_blank" rel="noopener" sx={{ display: 'inline-flex', alignItems: 'center' }} underline="hover">
			{title} <OpenInNewIcon fontSize="small" sx={{ ml: 0.5 }} />
		</Link>
	</Typography>
);

const RequestedDisciplineData = ({ requisitionData, takenDiscs, documents }) => (
	<Grid2 container rowSpacing={1} columnSpacing={2}>
		<Grid2
			size={8}
		>
			<Typography variant='h6'><strong>Disciplina Requerida</strong></Typography>
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
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Código</strong></Typography>
			</Grid2>
			<Grid2 size={4}>
				<Typography variant='body2'><strong>Nome</strong></Typography>
			</Grid2>
			<Grid2 size={3}>
				<Typography variant='body2'><strong>Departamento</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Tipo</strong></Typography>
			</Grid2>
			<Grid2 size={2} />
		</Grid2>

		<Grid2 size={1}><Typography variant='body2'>{'MAC0422' || requisitionData.requested_disc_code}</Typography></Grid2>
		<Grid2 size={4}>
			<Typography variant='body2'>{'Algoritmos e Estruturas de Dados para Engenharia Elétrica' || requisitionData.requested_disc}</Typography>
		</Grid2>
		<Grid2 size={3}>
			<Typography variant='body2'>{'Disciplina de fora do IME' || requisitionData.requested_disc}</Typography>
		</Grid2>
		<Grid2 size={4}>
			<Typography variant='body2'>{'Optativa Eletiva' || requisitionData.requested_disc}</Typography>
		</Grid2>
	</Grid2>
);

const CompletedDisciplinesData = ({ requisitionData, takenDiscs, documents }) => (
	<Grid2 container rowSpacing={1} columnSpacing={2}>
		<Grid2
			size={8}
		>
			<Typography variant='h6'><strong>Disciplina(s) Cursada(s)</strong></Typography>
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
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Código</strong></Typography>
			</Grid2>
			<Grid2 size={4}>
				<Typography variant='body2'><strong>Nome</strong></Typography>
			</Grid2>
			<Grid2 size={3}>
				<Typography variant='body2'><strong>Instituição</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Nota</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Ano</strong></Typography>
			</Grid2>
			<Grid2 size={1}>
				<Typography variant='body2'><strong>Semestre</strong></Typography>
			</Grid2>
		</Grid2>
		{takenDiscs.map((disc, _) => (
			<>
				<Grid2 size={1}><Typography variant='body2'>{disc.code}</Typography></Grid2>
				<Grid2 size={4}><Typography variant='body2'>{disc.name}</Typography></Grid2>
				<Grid2 size={3}><Typography variant='body2'>{disc.institution}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body2'>{disc.grade}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body2'>{disc.year}</Typography></Grid2>
				<Grid2 size={1}><Typography variant='body2'>{disc.semester}</Typography></Grid2>
			</>
		))}
    </Grid2>
);

const Reviews = ({ requisitionData }) => <>{
	<Grid2 container rowSpacing={1} columnSpacing={1.5}>
		<Grid2
			size={12}
 			//sx={{
 			//	backgroundColor: '#FDFFBE'
			//}}
		>
			<Typography variant='h6'><strong>Pareceres</strong></Typography>
		</Grid2>
		{requisitionData.reviews.map((review) => (
			<>
				<Grid2 size={1}>
					<Typography variant='body2'><strong>Nome:</strong></Typography>
				</Grid2>
				<Grid2 size={5}>
					<Typography variant='body2'>{review.reviewer_name}</Typography>
				</Grid2>
				<Grid2 size={1}>
					<Typography variant='body2'><strong>NUSP:</strong></Typography>
				</Grid2>
				<Grid2 size={3}>
					<Typography variant='body2'>{review.reviewer_nusp}</Typography>
				</Grid2>
				<Grid2 size={2} />

				<Grid2 size={1}>
					<Typography variant='body2'><strong>Decisão:</strong></Typography>
				</Grid2>
				<Grid2 size={5}>
					<Typography variant='body2'>{review.reviewer_decision}</Typography>
				</Grid2>
				<Grid2 size={1}>
					<Typography variant='body2'><strong>Últ. Mod.:</strong></Typography>
				</Grid2>
				<Grid2 size={2}>
					<Typography variant='body2'>{formatDate(review.updated_at)}</Typography>
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
					<Grid2 size={1}>
						<Typography variant='body2'><strong>Justif.:</strong></Typography>
					</Grid2>
					<Grid2 size={11}>
						<Typography variant='body2'>{requisitionData.observations}</Typography>
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

    const { user } = useUser();
    const userRoles = user?.roles || [];
    const roleId = user?.currentRoleId;
    const departmentId = user?.currentDepartmentId;
	console.log(user);

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
				elevation={2}
				sx={{
					padding: 1,
					width: '100%'
				}}
			>
				<Stack spacing={3} divider={<Divider orientation="horizontal" flexItem />}>
                    <RequisitionData requisitionData={requisitionData} />
                    <StudentData requisitionData={requisitionData} />
                    <RequestedDisciplineData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                    <CompletedDisciplinesData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                    <Reviews requisitionData={requisitionData} />
                </Stack>

			</Paper>
		</Stack>
	);
};

export default RequisitionDetail;

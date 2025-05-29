import React from 'react';
import RequisitionDetail from '../Features/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';
import { RequisitionProvider } from '../Features/RequisitionDetail/useRequisitionContext';
import { useTheme } from '@mui/material/styles';
import useMediaQuery from '@mui/material/useMediaQuery';
import { Box, Typography, Paper, Chip } from '@mui/material';

const RequisitionVersionDetailPage = ({
	label,
	requisition,
	event,
	takenDisciplines,
	documents
}) => {

	const theme = useTheme();
	const isMediumOrLarger = useMediaQuery(theme.breakpoints.up('md'));
	const actionsVariant = isMediumOrLarger ? 'box' : 'bar';

	const selectedActions = [];

	console.log("takenDisciplines", takenDisciplines);

	return (
		<RequisitionProvider requisitionData={requisition}>
			<BasePage
				headerProps={{
					label: label,
					isExit: false
				}}
				actionsProps={{
					selectedActions: selectedActions,
					variant: actionsVariant
				}}
			>
				<Box sx={{ mb: 3, width: {xs: "100%", md: "60%"}}}>
					<Paper elevation={2} sx={{ p: 3, mb: 2 }}>
						<Typography variant="h6" gutterBottom>
							Informações do Evento
						</Typography>
						<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 2, alignItems: 'center' }}>
							<Box>
								<Typography variant="subtitle2" color="text.secondary">
									Tipo de Evento:
								</Typography>
								<Chip
									label={event.type}
									color="primary"
									variant="outlined"
									sx={{ mt: 0.5 }}
								/>
							</Box>
							<Box>
								<Typography variant="subtitle2" color="text.secondary">
									Data:
								</Typography>
								<Typography variant="body1">
									{new Date(event.created_at).toLocaleDateString('pt-BR')} às {new Date(event.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
								</Typography>
							</Box>
							<Box>
								<Typography variant="subtitle2" color="text.secondary">
									Autor:
								</Typography>
								<Typography variant="body1">
									{event.author_name} ({event.author_nusp})
								</Typography>
							</Box>
							<Box>
								<Typography variant="subtitle2" color="text.secondary">
									Versão:
								</Typography>
								<Typography variant="body1">
									{event.version}
								</Typography>
							</Box>
						</Box>
						{event.message && (
							<Box sx={{ mt: 2 }}>
								<Typography variant="subtitle2" color="text.secondary">
									Mensagem:
								</Typography>
								<Typography variant="body1">
									{event.message}
								</Typography>
							</Box>
						)}
					</Paper>

					<Paper elevation={1} sx={{ p: 2, backgroundColor: 'warning.light', color: 'warning.contrastText' }}>
						<Typography variant="body2" sx={{ fontWeight: 'medium' }}>
							⚠️ Esta é uma visualização histórica do requerimento no momento do evento selecionado.
							Os dados apresentados refletem o estado do requerimento naquele momento específico.
						</Typography>
					</Paper>
				</Box>

				<RequisitionDetail
					takenDiscs={takenDisciplines}
					documents={documents}
					isReadOnly={true}
				/>
			</BasePage>
		</RequisitionProvider>
	);
};

export default RequisitionVersionDetailPage;

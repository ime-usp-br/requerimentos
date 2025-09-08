import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import DocumentLink from './DocumentLink';

const RequestedDisciplineData = ({ takenDiscs, documents }) => {
	const { requisitionData } = useRequisitionContext();

	return (
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

            <Grid2 size={1}><Typography variant='body1'>{'MAC0422' || requisitionData.requested_disc_code}</Typography></Grid2>
            <Grid2 size={4}>
                <Typography variant='body1'>{'Algoritmos e Estruturas de Dados para Engenharia Elétrica' || requisitionData.requested_disc}</Typography>
            </Grid2>
            <Grid2 size={3}>
                <Typography variant='body1'>{'Disciplina de fora do IME' || requisitionData.requested_disc}</Typography>
            </Grid2>
            <Grid2 size={4}>
                <Typography variant='body1'>{'Optativa Eletiva' || requisitionData.requested_disc}</Typography>
            </Grid2>
        </Grid2>
    );
};

export default RequestedDisciplineData;

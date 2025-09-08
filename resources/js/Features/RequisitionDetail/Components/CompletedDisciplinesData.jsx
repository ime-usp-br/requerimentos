import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import DocumentLink from './DocumentLink';

const CompletedDisciplinesData = ({ takenDiscs, documents }) => {
	const { requisitionData } = useRequisitionContext();

    return (
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
                    <Grid2 size={1}><Typography variant='body1'>{disc.code}</Typography></Grid2>
                    <Grid2 size={4}><Typography variant='body1'>{disc.name}</Typography></Grid2>
                    <Grid2 size={3}><Typography variant='body1'>{disc.institution}</Typography></Grid2>
                    <Grid2 size={1}><Typography variant='body1'>{disc.grade}</Typography></Grid2>
                    <Grid2 size={1}><Typography variant='body1'>{disc.year}</Typography></Grid2>
                    <Grid2 size={1}><Typography variant='body1'>{disc.semester}</Typography></Grid2>
                </>
            ))}
        </Grid2>
    );
};

export default CompletedDisciplinesData;

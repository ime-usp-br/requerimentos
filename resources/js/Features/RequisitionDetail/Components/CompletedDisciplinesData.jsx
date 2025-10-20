import React from 'react';
import { Typography, Grid2, Stack } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import DocumentLink from './DocumentLink';

const CompletedDisciplinesData = ({ takenDiscs, documents }) => {
    return (
        <Grid2 container rowSpacing={.8} columnSpacing={1} columns={24}>
            <Grid2 size={18}>
                <Typography variant='h6'><strong>Disciplina(s) Cursada(s)</strong></Typography>
            </Grid2>
            <Grid2
                container
                size={6}
                justifyContent='right'
                alignItems='end'
            >
                <Stack
                    direction='row'
                    justifyContent='right'
                    spacing={2}
                >
                    <DocumentLink title={'Ementa(s)'} doc={documents[1]} />
                    <DocumentLink title={'Histórico'} doc={documents[3]} />
                </Stack>
            </Grid2>

            <Grid2
                container
                size={24}
                sx={(theme) => ({
                    backgroundColor: theme.palette.orange.main,
                    borderRadius: 1
                })
                }
            >
                <Grid2 size={1} />
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Sigla</strong></Typography>
                </Grid2>
                <Grid2 size={7}>
                    <Typography variant='body2'><strong>Nome</strong></Typography>
                </Grid2>
                <Grid2 size={7}>
                    <Typography variant='body2'><strong>Instituição</strong></Typography>
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Nota</strong></Typography>
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Ano-Semestre</strong></Typography>
                </Grid2>
            </Grid2>
            {takenDiscs.map((disc, _) => (
                <>
                    <Grid2 size={1} />
                    <Grid2 size={3}><Typography variant='body2'>{disc.code}</Typography></Grid2>
                    <Grid2 size={7}><Typography variant='body2'>{disc.name}</Typography></Grid2>
                    <Grid2 size={7}><Typography variant='body2'>{disc.institution}</Typography></Grid2>
                    <Grid2 size={3}><Typography variant='body2'>{disc.grade}</Typography></Grid2>
                    <Grid2 size={3}><Typography variant='body2'>{disc.year + '-' + disc.semester}</Typography></Grid2>
                </>
            ))}
        </Grid2>
    );
};

export default CompletedDisciplinesData;

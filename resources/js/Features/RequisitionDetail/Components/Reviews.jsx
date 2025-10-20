import React from 'react';
import { Typography, Grid2, Paper } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';

const formatDate = (originalDate) => {
    const date = new Date(originalDate);
    const pad = (n) => n.toString().padStart(2, '0');
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())}`;
};

const Reviews = () => {
    const { requisitionData } = useRequisitionContext();
    console.log(requisitionData);

    return (
        <Grid2 container rowSpacing={.8} columns={24}>
            <Grid2
                size={24}
            //sx={{
            //	backgroundColor: '#FDFFBE'
            //}}
            >
                <Typography variant='h6'><strong>Pareceres</strong></Typography>
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
                    <Typography variant='body2'><strong>NUSP</strong></Typography>
                </Grid2>
                <Grid2 size={7}>
                    <Typography variant='body2'><strong>Parecerista</strong></Typography>
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Decisão</strong></Typography>
                </Grid2>
                <Grid2 size={10}>
                    <Typography variant='body2'><strong>Justificativa</strong></Typography>
                </Grid2>
            </Grid2>

            {requisitionData.reviews.map((review) => (<>
                <Grid2 size={1} />
                <Grid2 size={3}>
                    <Typography variant='body2'>{review.reviewer_nusp}</Typography>
                </Grid2>
                <Grid2 size={7}>
                    <Typography variant='body2'>{review.reviewer_name}</Typography>
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'>{review.reviewer_decision}</Typography>
                </Grid2>
                <Grid2
                    size={10}
                >
                    <Typography variant='body2' noWrap>{review.justification}</Typography>
                </Grid2>
            </>))}
        </Grid2>
    );
};

export default Reviews;

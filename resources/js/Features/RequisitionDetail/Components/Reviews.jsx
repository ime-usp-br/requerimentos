import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import FullTextTooltip from '../../FullTextTooltip';

const Reviews = () => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Grid2 container rowSpacing={.8} columns={24}>
            <Grid2
                size={24}
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
                <Grid2 size={6}>
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
                <Grid2 size={6}>
                    <FullTextTooltip value={review.reviewer_name} />
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'>{review.reviewer_decision}</Typography>
                </Grid2>
                <Grid2
                    size={11}
                >
                    <FullTextTooltip value={review.justification} />
                </Grid2>
            </>))}
        </Grid2>
    );
};

export default Reviews;

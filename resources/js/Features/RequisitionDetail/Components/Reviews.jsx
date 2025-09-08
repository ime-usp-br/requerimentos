import React from 'react';
import { Typography, Grid2, Paper } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';

const formatDate = (originalDate) => {
	const date = new Date(originalDate);
	const pad = (n) => n.toString().padStart(2, '0');
	return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const Reviews = () => {
	const { requisitionData } = useRequisitionContext();
    console.log(requisitionData);

	return (
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
                <Paper
                    elevation={2}
                    sx={{
                        width: '100%',
                        padding: 1
                    }}
                >
                    <Grid2 container size={12}>
                        <Grid2 size={1}>
                            <Typography variant='body1'><strong>Nome:</strong></Typography>
                        </Grid2>
                        <Grid2 size={5}>
                            <Typography variant='body1'>{review.reviewer_name}</Typography>
                        </Grid2>
                        <Grid2 size={1}>
                            <Typography variant='body1'><strong>NUSP:</strong></Typography>
                        </Grid2>
                        <Grid2 size={3}>
                            <Typography variant='body1'>{review.reviewer_nusp}</Typography>
                        </Grid2>
                        <Grid2 size={2} />

                        <Grid2 size={1}>
                            <Typography variant='body1'><strong>Decisão:</strong></Typography>
                        </Grid2>
                        <Grid2 size={5}>
                            <Typography variant='body1'>{review.reviewer_decision}</Typography>
                        </Grid2>
                        <Grid2 size={1}>
                            <Typography variant='body1'><strong>Últ. Mod.:</strong></Typography>
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
                            <Grid2 size={1}>
                                <Typography variant='body1'><strong>Justif.:</strong></Typography>
                            </Grid2>
                            <Grid2 size={11}>
                                <Typography variant='body1'>{review.justification}</Typography>
                            </Grid2>
                        </Grid2>
                    </Grid2>
                </Paper>
            ))}
        </Grid2>
    );
};

export default Reviews;

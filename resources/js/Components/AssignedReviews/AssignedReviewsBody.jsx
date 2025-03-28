import React from 'react';
import { Container, Paper, Stack, Typography  } from '@mui/material';

export default function AssignedReviewsBody({ reviews }) {
    return (
        <Container maxWidth="lg">
            {
            reviews.length == 0 ? 
                <Stack 
                    direction='row' 
                    justifyContent='center'
                    sx={{
                        marginTop: 30
                    }}
                >
                    <Typography variant='overline' sx={{
                        fontSize: { xs: 12, sm: 20 }
                    }}>
                        Não há pareceres para este requerimento
                    </Typography>
                </Stack>
            
            : reviews.map((review) => (
                    <Paper 
                        elevation={3}
                        sx={{ 
                            margin: 3,
                            padding: 2
                        }}
                    >
                        <Stack spacing={1}>
                            <Typography variant="h6" sx={{ fontWeight: 600 }}>Parecer de {review.reviewer_name}</Typography>
                            <Typography variant="body1"><strong>Número USP do Parecerista:</strong> {review.reviewer_nusp}</Typography>
                            <Typography variant="body1"><strong>Decisão:</strong> {review.reviewer_decision}</Typography>
                            <Typography variant="body1"><strong>Justificativa:</strong></Typography>
                            <Typography variant="body1">{review.justification}</Typography>
                        </Stack>
                    </Paper>
                ))
            }
        </Container>
    );   
}

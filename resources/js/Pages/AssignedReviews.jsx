import React from 'react';
import { Stack } from '@mui/material';

import Header from '../Features/Header/Header';
import AssignedReviewsBody from '../Features/AssignedReviews/AssignedReviewsBody';

export default function AssignedReviews({ label,
                                          selectedActions,
                                          reviews
                                        }) {
    return (
        <Stack 
            direction='column'
            spacing={{ xs: 3, sm: 0 }}
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <Header 
                label={label} 
                selectedActions={selectedActions} />
            <AssignedReviewsBody reviews={reviews} />
        </Stack>
    );
};
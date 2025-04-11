import React from 'react';
import { Stack } from '@mui/material';

import Header from '../Components/Header/Header';
import AssignedReviewsBody from '../Components/AssignedReviews/AssignedReviewsBody';

export default function AssignedReviews({ label,
                                          roleId,
                                          userRoles,
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
                roleId={roleId} 
                userRoles={userRoles}
                selectedActions={selectedActions} />
            <AssignedReviewsBody reviews={reviews} />
        </Stack>
    );
};
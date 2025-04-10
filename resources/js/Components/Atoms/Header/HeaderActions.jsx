import React from 'react';
import { router } from '@inertiajs/react'
import { Stack, Paper } from '@mui/material';

import ComboBox from '../ComboBox';
import Builder from '../../RequisitionList/RequisitionListBody/builder';
import buttonComponentList from '../../RequisitionList/RequisitionListBody/UserActions/buttonComponentList';

export default function HeaderActions({ roleId,
                                        showRoleSelector, 
                                        userRoles, 
                                        actionsParams,
                                        isExit }) {
    const handleComboBoxChange = (value) => {
        router.post(route('role.switch'), { 'role-switch': value.id });
    };

    let builder = new Builder(buttonComponentList);
    return (
        <Stack 
            direction='row'
            sx={{
                justifyContent: { sm: 'space-between', md: 'space-around' },
                alignItems: "center"
            }}
            spacing={2}
        >
            { showRoleSelector && (roleId != 0) && (userRoles.length > 1) && (
                <ComboBox
                    size='small'
                    options={userRoles}
                    optionGetter={(option) => option.name}
                    defaultValue={userRoles.find(val => val.id == roleId)}
                    sx={{
                        width: 250,
                    }}
                    name='papel'
                    onChange={handleComboBoxChange}
                />
            )}
            
            { builder.build(isExit ? ['exit'] : ['go_back']).map((itemBuilder) =>
                itemBuilder(actionsParams)
            ) }
                
        </Stack>
    );
};


// import { useDialogContext }

// [isOpen, setOpen, setClose ] = useDialogContext();
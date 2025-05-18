import React from 'react';
import { router } from '@inertiajs/react'
import { Stack } from '@mui/material';

import ComboBox from '../../ui/ComboBox';
import Builder from '../../ui/ComponentBuilder/Builder';
import buttonComponentList from '../../ui/ComponentBuilder/ButtonComponentList';

export default function HeaderActions({ 
    roleId,
    showRoleSelector,
    userRoles,
    actionsParams,
    isExit }) {
    const handleComboBoxChange = (value) => {
        console.log(value);
        router.post(
            route('role.switch'), 
            { 
                'roleId': value.role_id,
                'departmentId': value.department_id,
            }
        );
    };

    let builder = new Builder(buttonComponentList);
    const headerActionsButtonStyle = {
        variant: 'outlined',
        sx: {
            color: 'white',
            borderColor: 'white'
        }
    };

    let getRoleName = (option) => {
        if (!option.department)
            return option.role.name;

        return option.role.name + " do " + option.department.name;
    };

    return (
        <Stack
            direction='row'
            sx={{
                justifyContent: { sm: 'space-between', md: 'space-around' },
                alignItems: "center"
            }}
            spacing={2}
        >
            {showRoleSelector && (userRoles.length > 1) && (
                <ComboBox
                    size='small'
                    options={userRoles}
                    optionGetter={getRoleName}
                    defaultValue={userRoles.find(val => val.role_id == roleId)}
                    sx={{
                        width: 250,
                        "& .MuiInputLabel-root": { color: "white" },
                        "& .MuiInputLabel-root.Mui-focused": { color: "white" },
                        "& .MuiOutlinedInput-root": { color: "white" },
                        "& .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline": { borderColor: "white" },
                        "& .MuiOutlinedInput-root:hover .MuiOutlinedInput-notchedOutline": { borderColor: "white" },
                        "& .MuiOutlinedInput-root.Mui-focused .MuiOutlinedInput-notchedOutline": { borderColor: "white" },
                        "& .MuiAutocomplete-popupIndicator": { color: "white" },
                        "& .MuiSvgIcon-root": { color: "white" }
                    }}
                    name='papel'
                    onChange={handleComboBoxChange}
                />
            )}

            {builder.build(isExit ? ['exit'] : ['go_back']).map((itemBuilder) =>
                itemBuilder({ actionsParams, styles: headerActionsButtonStyle })
            )}

        </Stack>
    );
};
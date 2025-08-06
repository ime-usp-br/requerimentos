import React from 'react';
import { router } from '@inertiajs/react'
import { Stack } from '@mui/material';

import ComboBox from '../../ui/ComboBox';
import Builder from '../../ui/ComponentBuilder/Builder';
import buttonComponentList from '../../ui/ComponentBuilder/ButtonComponentList';
import { useUser } from '../../Context/useUserContext';
import { styled } from '@mui/material/styles';

const HeaderActionsContainer = styled(Stack)(({ theme }) => ({
    justifyContent: 'space-between',
    alignItems: 'center',
    [theme.breakpoints.up('md')]: {
        justifyContent: 'space-around',
    },
}));

const StyledComboBox = styled(ComboBox)(({ theme }) => ({
    width: 250,
    userSelect: 'none',
    '& .MuiInputLabel-root': { color: 'white', userSelect: 'none' },
    '& .MuiInputLabel-root.Mui-focused': { color: 'white' },
    '& .MuiOutlinedInput-root': { color: 'white', userSelect: 'none' },
    '& .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline': { borderColor: 'white' },
    '& .MuiOutlinedInput-root:hover .MuiOutlinedInput-notchedOutline': { borderColor: 'white' },
    '& .MuiOutlinedInput-root.Mui-focused .MuiOutlinedInput-notchedOutline': { borderColor: 'white' },
    '& .MuiAutocomplete-popupIndicator': { color: 'white' },
    '& .MuiSvgIcon-root': { color: 'white' }
}));

const headerActionsButtonStyle = {
    variant: 'outlined',
    sx: {
        color: 'white',
        borderColor: 'white'
    }
};

export default function HeaderActions({
    showRoleSelector,
    isExit }) {
    const { user } = useUser();
    const userRoles = user?.roles || [];
    const roleId = user?.currentRoleId;
    const departmentId = user?.currentDepartmentId;

    const handleComboBoxChange = (value) => {
        router.post(
            route('role.switch'),
            {
                'roleId': value.role_id,
                'departmentId': value.department_id,
            }
        );
    };

    let getRoleName = (option) => {
        if (!option.department)
            return option.role.name;

        return option.role.name + " " + option.department.code;
    };

    let builder = new Builder(buttonComponentList);

    return (
        <HeaderActionsContainer direction='row' spacing={2}>
            {showRoleSelector && (userRoles.length > 1) && (
                <StyledComboBox
                    size='small'
                    options={userRoles}
                    optionGetter={getRoleName}
                    value={userRoles.find(val => (val.role_id == roleId) && (val.department_id == departmentId)) || null}
                    name='Papel'
                    onChange={handleComboBoxChange}
                />
            )}

            {builder.build(isExit ? ['exit'] : ['go_back']).map((itemBuilder) =>
                itemBuilder({ styles: headerActionsButtonStyle })
            )}

        </HeaderActionsContainer>
    );
};
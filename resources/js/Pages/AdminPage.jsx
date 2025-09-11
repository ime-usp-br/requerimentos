import React from 'react';

import BasePage from './BasePage';
import ManageUsers from '../Features/Admin/ManageUsers';
import { useUser } from '../Context/useUserContext';

function AdminPage({ systemUsers }){
    const { isRole } = useUser();

    const selectedActions = [
        ...(isRole(2) ? ['requisition_period'] : []),
        'add_role'
    ];

    return (
        <BasePage
            headerProps={{
                label: "Administração do sistema",
                showRoleSelector: false
            }}
            actionsProps={{
                selectedActions: [selectedActions],
                variant: "bar"
            }}
        >
            <ManageUsers users={systemUsers} />
        </BasePage>
    );
};

export default AdminPage;

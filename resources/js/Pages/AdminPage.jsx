import React from 'react';

import BasePage from './BasePage';
import ManageUsers from '../Features/Admin/ManageUsers';

function AdminPage({ users }){
    return (
        <BasePage
            headerProps={{
                label: "Administração do sistema",
                isExit: false,
                showRoleSelector: false
            }}
            actionsProps={{
                selectedActions: [['requisition_period', 'add_role']],
                variant: "bar"
            }}
        >
            <ManageUsers users={users} />
        </BasePage>
    );
};

export default AdminPage;
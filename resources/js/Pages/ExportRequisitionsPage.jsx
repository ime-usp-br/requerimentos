import React from 'react';
import BasePage from './BasePage';
import ExportRequisitions from '../Features/ExportRequisitions/ExportRequisitions';

function ExportRequisitionsPage({ label, roleId, userRoles, options }) {
    return (
        <BasePage
            headerProps={{
                label: label,
                roleId: roleId,
                userRoles: userRoles,
                isExit: false
            }}
        >   
            <ExportRequisitions options={options} />    
        </BasePage>
    );
};

export default ExportRequisitionsPage;
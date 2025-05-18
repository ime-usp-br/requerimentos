import React from 'react';
import BasePage from './BasePage';
import ExportRequisitions from '../Features/ExportRequisitions/ExportRequisitions';

function ExportRequisitionsPage({ label, options }) {
    return (
        <BasePage
            headerProps={{
                label: label,
                isExit: false
            }}
        >   
            <ExportRequisitions options={options} />    
        </BasePage>
    );
};

export default ExportRequisitionsPage;
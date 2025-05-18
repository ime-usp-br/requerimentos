import React from "react";
import BasePage from "./BasePage";
import RequisitionForm from "../Components/RequisitionForm/RequisitionForm";

const RequisitionFormPage = ({ requisitionData, label, roleId, userRoles, isStudent, isUpdate }) => {
    return (
        <BasePage
            headerProps={{
                label: label,
                roleId: roleId,
                userRoles: userRoles,
                isExit: false
            }}>
            <RequisitionForm
                requisitionData={requisitionData}
                isStudent={isStudent}
                isUpdate={isUpdate}/>
        </BasePage>
    );
};

export default RequisitionFormPage;

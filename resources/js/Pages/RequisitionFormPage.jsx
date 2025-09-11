import React from "react";
import BasePage from "./BasePage";
import RequisitionForm from "../Features/RequisitionForm/RequisitionForm";

const RequisitionFormPage = ({ requisitionData, label, isStudent, isUpdate }) => {
    return (
        <BasePage
            headerProps={{
                label: label,
            }}>
            <RequisitionForm
                requisitionData={requisitionData}
                isStudent={isStudent}
                isUpdate={isUpdate}/>
        </BasePage>
    );
};

export default RequisitionFormPage;

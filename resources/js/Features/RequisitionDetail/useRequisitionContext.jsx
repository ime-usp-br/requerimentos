import React, { createContext, useContext } from 'react';

const RequisitionContext = createContext();

const useRequisitionContext = () => {
  return useContext(RequisitionContext);
};

const RequisitionProvider = ({ children, requisitionData = {} }) => {
  
  return (
    <RequisitionContext.Provider value={{ requisitionData }}>
      {children}
    </RequisitionContext.Provider>
  );
};

export { useRequisitionContext, RequisitionProvider };

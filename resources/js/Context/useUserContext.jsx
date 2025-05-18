import React, { createContext, useContext } from 'react';
import { usePage } from '@inertiajs/react';

const UserContext = createContext();

const useUser = () => {
  return useContext(UserContext);
};

const UserProvider = ({ children }) => {
  const { props } = usePage();
  const userData = props?.auth?.user || null;

  const isRole = (roleId, departmentId = null) => {
    if (!userData || !userData.currentRole) {
      return false;
    }

    const roleMatch = userData.currentRole.role_id === roleId;
    const departmentMatch =
      departmentId === null || userData.currentDepartment?.id === departmentId;

    return roleMatch && departmentMatch;
  };

  const value = {
    user: userData,
    isRole,
  };

  return (
    <UserContext.Provider value={value}>
      {children}
    </UserContext.Provider>
  );
};

export { UserProvider, useUser };
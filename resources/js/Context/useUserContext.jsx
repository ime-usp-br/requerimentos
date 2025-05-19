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

    const roleMatch = userData.currentRoleId === roleId;
    const departmentMatch =
      departmentId === null || userData.currentDepartmentId === departmentId;

    return roleMatch && departmentMatch;
  };

  return (
    <UserContext.Provider value={{ user: userData, isRole }}>
      {children}
    </UserContext.Provider>
  );
};

export { UserProvider, useUser };
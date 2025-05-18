import React from 'react';
import Builder from '../ComponentBuilder/Builder';
import buttonComponentList from '../ComponentBuilder/ButtonComponentList';
import ActionsMenuBar from './Components/ActionsMenuBar';
import ActionsMenuBox from './Components/ActionsMenuBox';

function ActionsMenu({ selectedActions, actionsParams, variant }) {
	const builder = new Builder(buttonComponentList);
	const MenuComponent = variant === 'box' ? ActionsMenuBox : ActionsMenuBar;

	return (
		<MenuComponent
			builder={builder}
			selectedActions={selectedActions}
			actionsParams={actionsParams}
		/>
	);
}

export default ActionsMenu;
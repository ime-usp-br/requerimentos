import React from 'react';
import { Stack } from '@mui/material';
import { styled } from '@mui/material/styles';
import Header from '../Features/Header/Header';
import ActionsMenu from '../ui/ActionsMenu/ActionsMenu';

const PageContainer = styled(Stack)({
	flexDirection: 'column',
	justifyContent: 'space-around',
	alignItems: 'center',
	width: '100%',
    height: '100vh',
	paddingBottom: 0
	// gap: "20px",
});

const ContentContainer = styled(Stack)(({ direction }) => ({
	flexDirection: direction,
	alignItems: 'flex-start',
	justifyContent: 'center',
	width: '100%',
	paddingTop: 4,
	gap: "20px",
}));

function BasePage({children, headerProps, actionsProps }){
	const direction = actionsProps?.variant === "box" ? "row" : "column";
	// actionsProps = false;
	return (
		<PageContainer>
			<Header {...headerProps} />
			<ContentContainer direction={direction}>
				{/* {actionsProps && <ActionsMenu {...actionsProps} />} */}
				{children}
			</ContentContainer>
		</PageContainer>
	);
};

export default BasePage;

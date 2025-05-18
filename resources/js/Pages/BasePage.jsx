import React from 'react';
import { Stack } from '@mui/material';
import { styled } from '@mui/material/styles';
import Header from '../Components/Header/Header';
import ActionsMenuBar from '../Components/Atoms/ActionsMenuBar';

const PageContainer = styled(Stack)({
    direction: 'column',
    justifyContent: 'space-around',
    alignItems: 'center',
    width: '100%',
    paddingBottom: 20,
	gap: "20px",
});

const ContentContainer = styled(Stack)({
	direction: 'column',
	spacing: 4,
	alignItems: 'top',
	justifyContent: 'center',
	width: '86%',
	paddingTop: 4,
	gap: "20px"
});

function BasePage({children, headerProps, actionsProps }){
    return (
        <PageContainer>
            <Header {...headerProps} />
            <ContentContainer>
				{actionsProps && <ActionsMenuBar {...actionsProps} />}
				{children}
			</ContentContainer>
        </PageContainer>
    );
};

export default BasePage;
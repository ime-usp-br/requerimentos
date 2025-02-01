import React from 'react';
import { Head } from '@inertiajs/react'
import styled from 'styled-components'

const H1 = styled.h1`
    color: blue;
`;

export default function Home() {
    return (
        <div>
            <Head title="Welcome" />
            <H1>Welcome</H1>
            <p>Hello boy, welcome to your first Inertia app!</p>
        </div>
    );
};
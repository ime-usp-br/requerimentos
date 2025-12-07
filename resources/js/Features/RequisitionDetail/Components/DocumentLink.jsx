import React from 'react';
import { Typography, Link } from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';

const DocumentLink = ({ title, doc }) => (
    <Link
        href={`/documents/${doc.id}/view`}
        target="_blank"
        rel="noopener"
        sx={{
            display: 'inline-flex',
            alignItems: 'right',
            justifyContent: 'center',
            height: '100%'
        }}
        underline="hover"
    >
        <Typography
            variant="body2"
            key={doc.id}
        >
            {title}
        </Typography>
        <OpenInNewIcon fontSize="small" sx={{ ml: 0.5 }} />
    </Link>
);

export default DocumentLink;

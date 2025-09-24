import React from 'react';
import { Typography, Link } from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';

const DocumentLink = ({ title, doc }) => (
	<Typography variant="body1" key={doc.id} align='right'>
		<Link href={`/documents/${doc.id}/view`} target="_blank" rel="noopener" sx={{ display: 'inline-flex', alignItems: 'center' }} underline="hover">
			{title} <OpenInNewIcon fontSize="small" sx={{ ml: 0.5 }} />
		</Link>
	</Typography>
);

export default DocumentLink;

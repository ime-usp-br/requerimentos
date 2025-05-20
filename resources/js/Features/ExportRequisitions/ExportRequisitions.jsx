import React from 'react';
import { Stack, Button } from '@mui/material';
import Filters from "./ExportRequisitionsFilters";
import { useForm } from '@inertiajs/react';
import axios from 'axios';

function ExportRequisitions({ options }) {
	const { data, setData } = useForm({
		internal_status: options.internal_statusOptions[0],
		department: options.departments[0],
		requested_disc_type: options.discTypes[0],
		start_date: null,
		end_date: null
	});

	const handleExport = async () => {
		try {
			const response = await axios.post(route('exportRequisitionsPost'), data, {
				responseType: 'blob'
			});

			const url = window.URL.createObjectURL(new Blob([response.data]));
			const link = document.createElement('a');
			link.href = url;
			const dateTimeStr = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
			link.setAttribute('download', `requisitions_export_${dateTimeStr}.xlsx`);
			document.body.appendChild(link);
			link.click();
			link.remove();
		} catch (error) {
			console.error('Export failed', error);
		}
	};

	return (
		<Stack
			spacing={5}
			sx={{
				alignItems: { xs: 'left', sm: 'baseline' },
			}}
			width="100%"
		>
			<Filters options={options} setData={setData} />
			<Button
				variant="contained"
				size="large"
				color="primary"
				onClick={handleExport}
			>
				Exportar
			</Button>
		</Stack>
	);
};

export default ExportRequisitions;
import React from 'react';
import { Stack, Typography, Grid2, FormControl, FormControlLabel, TextField, Radio, RadioGroup, Button } from '@mui/material';
import { useForm, router } from "@inertiajs/react";
import { useRequisitionContext } from '../useRequisitionContext';
import { useDialogContext } from '../../../Context/useDialogContext';
import { useUser } from '../../../Context/useUserContext';

import ActionSuccessful from '../../../Dialogs/ActionSuccessful';


const ResultForm = () => {
    const { user } = useUser();
    const roleId = user.currentRoleId;

    const { requisitionData } = useRequisitionContext();
    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();
    const { data, setData, post, processing, errors } = useForm({
        requisitionId: requisitionData.id,
        result: (roleId != 3 && requisitionData.result) || "",
        result_text: (roleId != 3 && requisitionData.result_text) || ""
    });

    const handleSubmit = (event) => {
        event.preventDefault();

        const submitRoute = roleId === 3 ? "submitReview" : "giveResultToRequisition";
        post(route(submitRoute), {
            onSuccess: (resp) => {
                closeDialog();
                setDialogTitle('Enviado com sucesso');
                setDialogBody(<ActionSuccessful dialogText={'O resultado foi enviado com sucesso.'} />);
                openDialog();
            },
            onError: (errors) => {
                if (typeof errors === 'string') {
                    setAlertText(errors);
                }
            }
        });
    };

    const handleReturn = () => {
        router.get(route('list'));
    };

    return (
        <form id="result-form" onSubmit={handleSubmit} autoComplete="off">
            <Stack spacing={1.5}>
                <Typography variant='h6'>
                    <strong>Dar {roleId === 3 ? "parecer" : "resultado"}</strong>
                </Typography>
                <FormControl component="fieldset" error={errors.result ? true : false}>
                    <RadioGroup
                        name="result"
                        value={data.result}
                        onChange={(e) => setData('result', e.target.value)}
                        row
                        sx={{
                            pl: 1
                        }}
                    >
                        {roleId === 2 &&
                            <FormControlLabel value="Inconsistência nas informações" control={<Radio />} label="Inconsistência nas informações" />
                        }
                        <FormControlLabel value="Deferido" control={<Radio />} label="Deferido" />
                        <FormControlLabel value="Indeferido" control={<Radio />} label="Indeferido" />
                        {roleId === 2 &&
                            <FormControlLabel value="Cancelado" control={<Radio />} label="Cancelado" />
                        }
                    </RadioGroup>
                    {errors.result && (
                        <Typography
                            variant="caption"
                            color="error"
                            sx={{ marginTop: '3px', marginLeft: '14px' }}
                        >
                            {errors.result}
                        </Typography>
                    )}
                </FormControl>
                <TextField
                    label="Observações"
                    variant="outlined"
                    fullWidth
                    multiline
                    rows={4}
                    name="result_text"
                    value={data.result_text}
                    onChange={(e) => setData('result_text', e.target.value)}
                    error={errors.result_text ? true : false}
                    helperText={errors.result_text}
                />
                <Stack
                    direction="row"
                    sx={{
                        justifyContent: "space-between",
                        alignItems: "center",
                    }}
                >
                    <Button size="medium" variant="text" color="primary" onClick={handleReturn}>Voltar</Button>
                    <Button size="medium" variant="contained" color="primary" onClick={handleSubmit}>Enviar</Button>
                </Stack>
            </Stack>
        </form>
    );
};

export default ResultForm;

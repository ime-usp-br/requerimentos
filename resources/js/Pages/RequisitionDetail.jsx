import React from 'react';
import { Container, Paper, Typography, Stack, Divider, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, useTheme, useMediaQuery  } from '@mui/material';

const RequisitionDetail = ({ req, takenDiscs, takenDiscsRecords, currentCourseRecords, takenDiscSyllabi, requestedDiscSyllabi }) => {
    // Está duplicado
    const theme = useTheme();
    const isLg = useMediaQuery(theme.breakpoints.up('lg'));

    const getElevation = () => {
        if (isLg) return 3;
        return 0;
    };

    return (
        <Container maxWidth="lg">
            <Paper elevation={getElevation()} sx={{ margin: {lg: 2}, padding: { lg: 2 } }}>
                <Typography variant="h4" component="h1" gutterBottom>
                    Detalhes do Requerimento
                </Typography>
                <Stack spacing={3} divider={<Divider orientation="horizontal" flexItem />}>
                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Dados Pessoais</Typography>
                        <Typography variant="body2"><strong>Nome:</strong> {req.student_name}</Typography>
                        <Typography variant="body2"><strong>Email:</strong> {req.email}</Typography>
                        <Typography variant="body2"><strong>Número USP:</strong> {req.student_nusp}</Typography>
                    </Stack>

                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Curso Atual</Typography>
                        <Typography variant="body2">{req.course}</Typography>
                    </Stack>

                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Disciplina Requerida</Typography>
                        <Typography variant="body2"><strong>Código:</strong> {req.requested_disc_code}</Typography>
                        <Typography variant="body2"><strong>Nome:</strong> {req.requested_disc}</Typography>
                        <Typography variant="body2"><strong>Tipo:</strong> {req.requested_disc_type}</Typography>
                        <Typography variant="body2"><strong>Departamento:</strong> {req.department}</Typography>
                    </Stack>

                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Disciplinas Cursadas</Typography>
                        <TableContainer component={Paper}>
                            <Table>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Nome</TableCell>
                                        <TableCell>Instituição</TableCell>
                                        <TableCell>Código</TableCell>
                                        <TableCell>Ano</TableCell>
                                        <TableCell>Nota</TableCell>
                                        <TableCell>Semestre</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {takenDiscs.map((disc, index) => (
                                        <TableRow key={index}>
                                            <TableCell>{disc.name}</TableCell>
                                            <TableCell>{disc.institution}</TableCell>
                                            <TableCell>{disc.code}</TableCell>
                                            <TableCell>{disc.year}</TableCell>
                                            <TableCell>{disc.grade}</TableCell>
                                            <TableCell>{disc.semester}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </TableContainer>
                    </Stack>

                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Documentos</Typography>
                        <Typography variant="body2"><strong>Histórico Escolar da Instituição de Origem:</strong> {takenDiscsRecords.name}</Typography>
                        <Typography variant="body2"><strong>Histórico Escolar do Curso Atual:</strong> {currentCourseRecords.name}</Typography>
                        <Typography variant="body2"><strong>Ementas das Disciplinas Cursadas:</strong> {takenDiscSyllabi.name}</Typography>
                        <Typography variant="body2"><strong>Ementa da Disciplina a Ser Dispensada:</strong> {requestedDiscSyllabi.name}</Typography>
                    </Stack>

                    <Stack spacing={1}>
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>Observações</Typography>
                        <Typography variant="body2">{req.observations}</Typography>
                    </Stack>
                </Stack>
            </Paper>
        </Container>
    );
};

export default RequisitionDetail;
import React from 'react';
import { Stack, Typography, Button, Alert, Box } from "@mui/material";
import { styled } from '@mui/material/styles';
import { CloudUpload } from '@mui/icons-material';

const VisuallyHiddenInput = styled('input')({
    clip: 'rect(0 0 0 0)',
    clipPath: 'inset(50%)',
    height: 1,
    overflow: 'hidden',
    position: 'absolute',
    bottom: 0,
    left: 0,
    whiteSpace: 'nowrap',
    width: 1,
  });

const DocumentsUpload = ({data, setData}) => {
    return (
        <Stack spacing={1.5}>
            <Typography variant={"h6"} component={"legend"}> Documentos </Typography>
            <Alert severity="info"> 
                Adicione apenas documentos relacionados às matérias desse pedido.
            </Alert>

            <Box>
                <Typography variant={"subtitle1"}> Histórico escolar da instituição de origem (PDF, max 512KB)</Typography>
                <Stack direction={{ sm: "row"}} spacing={{ sm: 2}} sx={{alignItems: "center"}}>
                    <Button
                        component="label"
                        variant="contained"
                        startIcon={<CloudUpload />}
                    >
                        Anexar arquivo
                        <VisuallyHiddenInput
                            type="file"
                            onChange={(e) => setData("takenDiscRecord", e.target.files[0])}
                            accept=".pdf"
                            required
                        />
                    </Button>
                    {data.takenDiscRecord && (
                        <Typography variant={"body2"}>Arquivo selecionado: {data.takenDiscRecord.name}</Typography>
                    )}
                </Stack>
            </Box>

            <Box>
                <Typography variant={"subtitle1"}> Histórico escolar do curso atual (máx. 512KB)</Typography>
                <Stack direction={{ sm: "row"}} spacing={{ sm: 2}} sx={{alignItems: "center"}}>
                    <Button
                        component="label"
                        variant="contained"
                        startIcon={<CloudUpload />}
                    >
                        Anexar arquivo
                        <VisuallyHiddenInput
                            type="file"
                            onChange={(e) => setData("courseRecord", e.target.files[0])}
                            accept=".pdf"
                            required
                        />
                    </Button>
                    {data.courseRecord && (
                        <Typography variant={"body2"}>Arquivo selecionado: {data.courseRecord.name}</Typography>
                    )}
                </Stack>
            </Box>

            <Box>
                <Typography variant={"subtitle1"}> Ementas das disciplinas cursadas (máx. 512KB)</Typography>
                <Stack direction={{ sm: "row"}} spacing={{ sm: 2}} sx={{alignItems: "center"}}>
                    <Button
                        component="label"
                        variant="contained"
                        startIcon={<CloudUpload />}
                    >
                        Anexar arquivo
                        <VisuallyHiddenInput
                            type="file"
                            onChange={(e) => setData("takenDiscSyllabus", e.target.files[0])}
                            accept=".pdf"
                            required
                        />
                    </Button>
                    {data.takenDiscSyllabus && (
                        <Typography variant={"body2"}>Arquivo selecionado: {data.takenDiscSyllabus.name}</Typography>
                    )}
                </Stack>
            </Box>

            <Box>
                <Typography variant={"subtitle1"}> Ementa da disciplina a ser dispensada (máx. 512KB)</Typography>
                <Stack direction={{ sm: "row"}} spacing={{ sm: 2}} sx={{alignItems: "center"}}>
                    <Button
                        component="label"
                        variant="contained"
                        startIcon={<CloudUpload />}
                    >
                        Anexar arquivo
                        <VisuallyHiddenInput
                            type="file"
                            onChange={(e) => setData("requestedDiscSyllabus", e.target.files[0])}
                            accept=".pdf"
                            required
                        />
                    </Button>
                    {data.requestedDiscSyllabus && (
                        <Typography variant={"body2"}>Arquivo selecionado: {data.requestedDiscSyllabus.name}</Typography>
                    )}
                </Stack>
            </Box>
        </Stack>
    );
};

export default DocumentsUpload;
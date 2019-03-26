import React from "react";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
    Paper,
} from "@material-ui/core";
import SaveAlt from "@material-ui/icons/SaveAlt";

class Report extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Paper>
                <Table>
                <TableHead>
                    <TableRow>
                    <TableCell>Название</TableCell>
                    <TableCell>Скачать</TableCell>
                </TableRow>
                </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell component="th" scope="row">
                                Сводный отчет
                            </TableCell>
                            <TableCell><a href="/api/v1/report/summary" target={"_blank"}><SaveAlt /></a></TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell component="th" scope="row">
                                По форме отеля
                            </TableCell>
                            <TableCell><a href="/api/v1/report/hotel" target={"_blank"}><SaveAlt /></a></TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell component="th" scope="row">
                                Для охраны
                            </TableCell>
                            <TableCell><a href="/api/v1/report/security" target={"_blank"}><SaveAlt /></a></TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </Paper>
        );
    }
}
export default Report;

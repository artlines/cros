import React from 'react';
import PropTypes from 'prop-types';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Table,
    TableBody,
    TableRow,
    TableCell,
    Grid,
} from '@material-ui/core';
import map from "lodash/map";
import LinearProgress from '../utils/LinearProgress';

class MakeInvoiceModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { open } = this.state;
        const { update } = this.props;

        if (prevState.open !== open && open) {
            update();
        }
    };

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    render() {
        const { trigger, organizationName } = this.props;
        const { open } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'md'}
                >
                    <DialogTitle>
                        <Grid
                            container
                            spacing={0}
                            justify={`space-between`}
                            alignItems={`center`}
                        >
                            <Grid item>
                                Выставить счет для {organizationName}
                            </Grid>
                            <Grid item>
                                {/*<FabButton title={`Добавить счет`} onClick={this.openForm}/>*/}
                            </Grid>
                        </Grid>
                    </DialogTitle>
                    <DialogContent>
                        {/*<LinearProgress show={isFetching}/>*/}
                        <Table>
                            <TableBody>

                            </TableBody>
                        </Table>
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={this.handleClose}>Закрыть</Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

MakeInvoiceModal.propTypes = {
    /** Trigger to open modal */
    trigger: PropTypes.node.isRequired,

    /** Organization info */
    organizationId:     PropTypes.number.isRequired,
    organizationName:   PropTypes.string.isRequired,

    update: PropTypes.func,
};

MakeInvoiceModal.defaultProps = {
    update: () => {},
};

export default MakeInvoiceModal;
import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogActions,
    DialogContent,
} from '@material-ui/core';
import map from "lodash/map";
import API from '../../libs/api';
import LinearProgress from '../utils/LinearProgress';
import abode from "../../actions/abode";

const api = new API();

class ReservedPlacesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            submitting: false,
        };
    }

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    render() {
        const { trigger } = this.props;
        const { open, submitting } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'sm'}
                >
                    <DialogTitle>
                        Резервирование мест
                    </DialogTitle>
                    <DialogContent>
                        <LinearProgress show={submitting}/>
                        asdasd
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={this.handleClose}>Закрыть</Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

ReservedPlacesModal.propTypes = {
    /** Trigger */
    trigger: PropTypes.node.isRequired,

    /** Will called after success submit */
    onSuccessSubmit: PropTypes.func.isRequired,
};

const mapStateToProps = state =>
    ({
        housing: state.abode.housing,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchHousings: () => dispatch(abode.fetchHousings()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(ReservedPlacesModal);
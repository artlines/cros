import React from 'react';
import PropTypes from 'prop-types';
import Button from '@material-ui/core/Button';
import {
    Dialog,
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
} from '@material-ui/core';

class ConfirmDialog extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
        }
    }

    handleClickOpen = (event) => {
        event.preventDefault();
        this.setState({open: true});
    };

    handleClose = () => {
        this.setState({open: false});
        this.props.onCancel();
    };

    handleSubmit = () => {
        this.setState({open: false});
        this.props.onConfirm();
    };

    render() {
        const { title, text, confirm, cancel, trigger } = this.props;

        return (
            <div>
                <span onClick={this.handleClickOpen}>{trigger}</span>
                <Dialog open={this.state.open} onClose={this.handleClose} >
                    <DialogTitle>{title}</DialogTitle>
                    <DialogContent>
                        <DialogContentText>{text}</DialogContentText>
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={this.handleClose} color="primary">
                            {cancel}
                        </Button>
                        <Button onClick={this.handleSubmit} color="primary" autoFocus>
                            {confirm}
                        </Button>
                    </DialogActions>
                </Dialog>
            </div>
        );
    }
}

ConfirmDialog.propTypes = {
    onConfirm:  PropTypes.func.isRequired,

    title:      PropTypes.string,
    text:       PropTypes.string,
    confirm:    PropTypes.string,
    cancel:     PropTypes.string,
    onCancel:   PropTypes.func,
    trigger:    PropTypes.node,
};

ConfirmDialog.defaultProps = {
    title:      'Требуется подтверждение',
    text:       'Подтвердите выполнение действия',
    confirm:    'Да',
    cancel:     'Нет',
    onCancel:   () => {},
};

export default ConfirmDialog;
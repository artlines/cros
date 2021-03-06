import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Divider,
    Grid,
    Chip,
    TextField,
    Typography,
} from '@material-ui/core';
import map from 'lodash/map';
import DateTime from "../utils/DateTime";
import LinearProgress from '../utils/LinearProgress';
import API from '../../libs/api';

const api = new API();

class CommentsModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            comment: '',
            submitting: false,
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        if (this.state.open && !prevState.open) {
            this.update();
        }
    }

    update = () => {
        const { organizationId, update } = this.props;
        update({conference_organization_id: organizationId});
    };

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    handleChangeComment = (event) => this.setState({comment: event.target.value});

    submit = () => {
        const { organizationId, update } = this.props;
        const { comment } = this.state;

        this.setState({submitting: true});

        api.post(`comment/new`, {
            content: comment,
            organization_id: organizationId,
            is_private: true,
        })
            .then(res => {
                this.setState({
                    submitting: false,
                    comment: '',
                });
                update({conference_organization_id: organizationId});
            });
    };

    render() {
        const { trigger, items, organizationName, isFetching } = this.props;
        const { open, submitting, comment } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'md'}
                >
                    <DialogTitle>Комментарии по {organizationName}</DialogTitle>
                    <DialogContent>
                        <Grid
                            container
                            spacing={16}
                            style={{
                                fontFamily: '"Roboto", "Helvetica", "Arial", sans-serif',
                            }}
                        >
                            <Grid item xs={12}>
                                <LinearProgress show={isFetching || submitting}/>
                            </Grid>
                            {map(items, item =>
                                <React.Fragment key={item.id}>
                                    <Grid item xs={12} style={{margin: '0 0 8'}}>
                                        <Grid
                                            container
                                            spacing={16}
                                            justify={'space-between'}
                                        >
                                            <Grid item>
                                                <Chip variant={'outlined'} label={item.user.name}/>
                                            </Grid>
                                            <Grid item>
                                                <Typography variant={`body2`}>
                                                    <DateTime withTime value={item.created_at}/>
                                                </Typography>
                                            </Grid>
                                            <Grid item xs={12}>
                                                <Typography variant={`body2`} style={{whiteSpace: 'pre-wrap'}}>
                                                    {item.content}
                                                </Typography>
                                            </Grid>
                                        </Grid>
                                    </Grid>
                                    <Divider/>
                                </React.Fragment>
                            )}
                        </Grid>
                    </DialogContent>
                    <DialogContent style={{paddingTop: 24}}>
                        <TextField
                            fullWidth
                            multiline
                            rows={3}
                            variant={'outlined'}
                            label={`Комментарий`}
                            value={comment}
                            onChange={this.handleChangeComment}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button
                            color={`primary`}
                            disabled={comment === ''}
                            onClick={this.submit}
                        >Отправить</Button>
                        <Button onClick={this.handleClose}>Закрыть</Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

CommentsModal.propTypes = {
    /**
     * Invoices array
     */
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id:         PropTypes.number.isRequired,
            created_at: PropTypes.number.isRequired,
            user:    PropTypes.shape({
                id:     PropTypes.number.isRequired,
                name:   PropTypes.string.isRequired,
                email:  PropTypes.string.isRequired,
            }),
            content:    PropTypes.string.isRequired,
            is_private: PropTypes.bool.isRequired,
        }),
    ),

    /**
     * Trigger
     */
    trigger: PropTypes.node.isRequired,

    /**
     * Organization info
     */
    organizationId:     PropTypes.number.isRequired,
    organizationName:   PropTypes.string.isRequired,
};

const mapStateToProps = state => ({
    ...state.participating.comment,
});

export default connect(mapStateToProps)(CommentsModal);
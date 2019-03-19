import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import PropTypes from 'prop-types';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Tooltip,
    Typography,
} from '@material-ui/core';
import {withStyles} from '@material-ui/core/styles';
import { blue, green } from '@material-ui/core/colors';
import {
    Close as CloseIcon,
    Edit as EditIcon,
    CheckCircle as CheckCircleIcon,
} from '@material-ui/icons';
import map from 'lodash/map';
import find from 'lodash/find';
import MemberForm from "./MemberForm";
import ConfirmDialog from "../utils/ConfirmDialog";
import FabButton from '../utils/FabButton';
import LinearProgress from '../utils/LinearProgress';
import API from '../../libs/api';
import RoomType from "../../containers/RoomType";

const styles = () =>
    ({
        noWrap: {
            whiteSpace: 'nowrap',
        },
    });

const api = new API();

class MembersModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            form: {
                open: false,
                initialValues: {},
            },
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

    delete = (id) => {
        api.delete(`conference_member/${id}`)
            .then(this.update);
    };

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    openForm = (id) => {
        const { items, organizationId } = this.props;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: {
                    conference_organization_id: organizationId,
                    ...id ? find(items, {id}) : {},
                },
            },
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { classes, organizationName, items, trigger, isFetching } = this.props;
        const { open, form } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <MemberForm
                    open={form.open}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                    initialValues={form.initialValues}
                />
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
                                Участники {organizationName}
                            </Grid>
                            <Grid item>
                                <FabButton title={`Добавить участника`} onClick={this.openForm}/>
                            </Grid>
                        </Grid>
                    </DialogTitle>
                    <DialogContent>
                        <LinearProgress show={isFetching}/>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>ФИО</TableCell>
                                    <TableCell className={classes.noWrap}>Тип комнаты</TableCell>
                                    <TableCell>Контакты</TableCell>
                                    <TableCell>Проживание</TableCell>
                                    <TableCell align={'right'}>Действия</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            <React.Fragment>
                                                {item.last_name} {item.first_name} {item.middle_name}
                                                {item.representative
                                                    ?
                                                        <Tooltip title={`Представитель`}>
                                                            <CheckCircleIcon style={{fontSize: 14, color: green[700]}}/>
                                                        </Tooltip>
                                                    : ''
                                                }
                                            </React.Fragment>
                                            <Typography variant={`caption`}>{item.post}</Typography>
                                        </TableCell>
                                        <TableCell className={classes.noWrap}>
                                            <RoomType id={item.room_type_id}/>
                                        </TableCell>
                                        <TableCell>
                                            <div className={classes.noWrap}><b>Телефон:</b> {item.phone}</div>
                                            <div className={classes.noWrap}><b>Email:</b> {item.email}</div>
                                        </TableCell>
                                        <TableCell >
                                            {item.apart_num
                                                ? <div className={classes.noWrap}>
                                                    <b>Номер:</b> {item.apart_num}
                                                </div>
                                                : 'Не заселен'
                                            }
                                        </TableCell>
                                        <TableCell align={'right'}>
                                                    <Button onClick={() => this.openForm(item.id)}><EditIcon/></Button>

                                                    <ConfirmDialog
                                                        trigger={<Button><CloseIcon/></Button>}
                                                        onConfirm={() => this.delete(item.id)}
                                                    />
                                        </TableCell>
                                    </TableRow>
                                )}
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

MembersModal.propTypes = {
    /**
     * Trigger
     */
    trigger: PropTypes.node.isRequired,

    /**
     * Organization name
     */
    organizationId: PropTypes.number,
    organizationName: PropTypes.string,

    /**
     * Invoices array
     */
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id:             PropTypes.number.isRequired,
            first_name:     PropTypes.string.isRequired,
            last_name:      PropTypes.string.isRequired,
            middle_name:    PropTypes.string,
            phone:          PropTypes.string.isRequired,
            post:           PropTypes.string,
            email:          PropTypes.string.isRequired,
            apart_num:      PropTypes.number,
            room_type_id:   PropTypes.number.isRequired,
        }),
    ),

    classes: PropTypes.object.isRequired,
};

const mapStateToProps = state => ({
    ...state.participating.member,
});

export default compose(
    connect(mapStateToProps),
    withStyles(styles),
)(MembersModal);
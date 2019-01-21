import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
} from '@material-ui/core';
import map from 'lodash/map';

class MembersModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
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

    render() {
        const { organizationName, items, trigger } = this.props;
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
                    <DialogTitle>Участники {organizationName}</DialogTitle>
                    <DialogContent>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>ФИО</TableCell>
                                    <TableCell>Должность</TableCell>
                                    <TableCell>Контакты</TableCell>
                                    <TableCell>Проживание</TableCell>
                                    <TableCell align={'right'}>Действия</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.last_name} {item.first_name} {item.middle_name}
                                        </TableCell>
                                        <TableCell>
                                            {item.post}
                                        </TableCell>
                                        <TableCell>
                                            <div><b>Телефон:</b> {item.phone}</div>
                                            <div><b>Email:</b> {item.email}</div>
                                        </TableCell>
                                        <TableCell>
                                            {item.place.room_num
                                                ? <React.Fragment>
                                                    <div><b>Заселен в номер:</b> {item.place.room_num}</div>
                                                    <div><b>Подтвержден:</b>
                                                        {item.place.approved
                                                            ? <span style={{color: 'green'}}>Да</span>
                                                            : <span style={{color: 'red'}}>Нет</span>
                                                        }
                                                    </div>
                                                </React.Fragment>
                                                : 'Не заселен'
                                            }
                                        </TableCell>
                                        <TableCell align={'right'}>
                                            <Button>Удалить</Button>
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
            middle_name:    PropTypes.string.isRequired,
            phone:          PropTypes.string.isRequired,
            post:           PropTypes.string.isRequired,
            email:          PropTypes.string.isRequired,
            place:          PropTypes.shape({
                room_num: PropTypes.oneOfType([PropTypes.number, null]),
                approved: PropTypes.oneOfType([PropTypes.bool, null]),
            }),
        }),
    ),
};

const mapStateToProps = state => ({
    ...state.participating.member,
});

export default connect(mapStateToProps)(MembersModal);
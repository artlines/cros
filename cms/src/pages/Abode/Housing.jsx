import React from 'react';
import {connect} from 'react-redux';
import abode from '../../actions/abode';
import {
    Grid,
    Typography,
} from '@material-ui/core';
import ApartmentsAddForm from '../../components/Abode/ApartmentsAddForm';
import AddButton from '../../components/utils/AddButton';

class Housing extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                initialValues: {},
                open: false,
            },
        };
    }

    componentDidMount() {
        this.props.fetchHousing();
        this.props.loadData();
    }

    openApartmentsAddForm = () => {
        const { housing: { id: housing_id } } = this.props;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: {housing_id},
            },
        });
    };

    handleCloseApartmentsAddForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { housing: { isFetching, error, id, title } } = this.props;
        const { form: { initialValues, open } } = this.state;

        return (
            <div>
                <ApartmentsAddForm
                    open={open}
                    initialValues={initialValues}
                    onClose={this.handleCloseApartmentsAddForm}
                    onSuccess={() => {}}
                />
                <Grid container spacing={24}>
                    <Grid item xs={12}>
                        <Grid container spacing={0} justify={`space-between`}>
                            <Grid item>
                                <Typography variant={`h5`} component={`span`}>{title}</Typography>
                            </Grid>
                            <Grid item>
                                <AddButton title={`Добавить апартаменты`} onClick={this.openApartmentsAddForm}/>
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        housing: state.abode.housing.item,
    });

const mapDispatchToProps = (dispatch, ownProps) =>
    ({
        fetchHousing: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(abode.fetchHousing(id));
        },
        loadData: () => {
            dispatch(abode.fetchApartmentTypes());
            dispatch(abode.fetchRoomTypes());
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(Housing);
import React from 'react';
import {connect} from 'react-redux';
import participating from '../actions/participating';
import ConferenceForm from '../components/Conference/Form';
import ConferenceTable from "../components/Conference/Table";
import find from "lodash/find";

class Conferences extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                open: false,
                initialValues: {},
            },
        };
    }

    componentDidMount() {
        this.update();
    }

    update = () => {
        this.props.fetchConferences();
    };

    openForm = (id) => {
        const { items } = this.props.conference;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: id ? find(items, {id}) : {},
            }
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { form } = this.state;
        const { conference } = this.props;

        return (
            <React.Fragment>
                <ConferenceForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                />
                <ConferenceTable
                    {...conference}
                    update={this.update}
                    onEdit={this.openForm}
                />
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        conference: state.participating.conference,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchConferences: () => dispatch(participating.fetchConferences()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Conferences);
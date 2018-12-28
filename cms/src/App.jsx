import React from 'react';
import CssBaseline from '@material-ui/core/CssBaseline';
import Sidebar from 'components/common/Sidebar';
import Header from 'components/common/Header';
import Routes from "./Routes";

class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarOpen: false,
        };
    }

    toggleSidebar = () => {
        this.setState({sidebarOpen: !this.state.sidebarOpen});
    };

    render() {
        return (
            <div>
                <CssBaseline/>
                <Header handleClickMenu={this.toggleSidebar}/>
                <Sidebar open={this.state.sidebarOpen} onClose={this.toggleSidebar}/>

                <Routes/>
            </div>
        );
    }
}

export default App;
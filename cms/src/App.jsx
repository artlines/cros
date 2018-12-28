import React from 'react';
import CssBaseline from '@material-ui/core/CssBaseline';
import Sidebar from 'components/common/Sidebar';
import Header from 'components/common/Header';

class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarOpen: false,
        };
    }

    toggleSidebar = () => {
        console.log(`handleToggleSidebar`);
        this.setState({sidebarOpen: !this.state.sidebarOpen});
    };

    render() {
        return (
            <div>
                <CssBaseline/>
                <Header handleClickMenu={this.toggleSidebar}/>
                <Sidebar open={this.state.sidebarOpen} onClose={this.toggleSidebar}/>
            </div>
        );
    }
}

export default App;
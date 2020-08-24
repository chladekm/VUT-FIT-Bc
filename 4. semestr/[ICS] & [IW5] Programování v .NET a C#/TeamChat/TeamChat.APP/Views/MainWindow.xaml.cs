using System.Security;
using System.Windows;
using System.Windows.Controls;
using TeamChat.APP.Views;

namespace TeamChat.APP.Views
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            TeamListView tlv = new TeamListView();
            
        }
    }
}

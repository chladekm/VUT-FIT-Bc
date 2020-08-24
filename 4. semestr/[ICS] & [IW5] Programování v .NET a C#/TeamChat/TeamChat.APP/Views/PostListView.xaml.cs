using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace TeamChat.APP.Views
{
    /// <summary>
    /// Interakční logika pro PostListView.xaml
    /// </summary>
    public partial class PostListView
    {
        public PostListView()
        {
            InitializeComponent();
        }

        private void AddNewPersonButton(object sender, RoutedEventArgs e)
        {
            AddNewPersonWindow anpw = new AddNewPersonWindow();
            anpw.Show();
        }
    }
}

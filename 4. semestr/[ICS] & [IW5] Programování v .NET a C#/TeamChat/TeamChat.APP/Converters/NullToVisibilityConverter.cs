using System;
using System.Globalization;
using System.Windows;
using System.Windows.Data;

namespace TeamChat.App.Converters
{
    public class NullToVisibilityConverter : IValueConverter
    {
        public Object Convert(Object value, Type targetType, Object parameter, CultureInfo culture) => value == null ?  Visibility.Hidden : Visibility.Visible;

        public Object ConvertBack(Object value, Type targetType, Object parameter, CultureInfo culture) => DependencyProperty.UnsetValue;
    }
}
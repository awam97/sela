import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Check if API session token exists
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('api_token');
  final bool isLoggedIn = token != null && token.isNotEmpty;

  runApp(SelaMobileApp(isLoggedIn: isLoggedIn));
}

class SelaMobileApp extends StatelessWidget {
  final bool isLoggedIn;

  const SelaMobileApp({Key? key, required this.isLoggedIn}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'صلة - إدارة المدارس والجمعيات',
      debugShowCheckedModeBanner: false,
      
      // Strict Arabic RTL Localization Configurations
      locale: const Locale('ar', 'AE'),
      supportedLocales: const [
        Locale('ar', 'AE'),
      ],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],

      // Sela Premium Design System (Navy and Gold)
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: const Color(0xff192a56),
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xff192a56),
          primary: const Color(0xff192a56),
          secondary: const Color(0xffc5a021),
          surface: Colors.white,
          background: const Color(0xfff4f6fa),
        ),
        
        // Cairo Font is the undisputed standard for luxury Arabic interfaces
        textTheme: GoogleFonts.cairoTextTheme(
          Theme.of(context).textTheme,
        ).apply(
          bodyColor: const Color(0xff2d3748),
          displayColor: const Color(0xff192a56),
        ),
        
        appBarTheme: AppBarTheme(
          backgroundColor: const Color(0xff192a56),
          foregroundColor: Colors.white,
          elevation: 0,
          centerTitle: true,
          titleTextStyle: GoogleFonts.cairo(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xff192a56),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 14),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            textStyle: GoogleFonts.cairo(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ),
      
      home: isLoggedIn ? const DashboardScreen() : const LoginScreen(),
    );
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'screens/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Check if API session token exists
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('api_token');
  final role = prefs.getString('role');
  final bool isLoggedIn = token != null && token.isNotEmpty && role != 'super_admin';

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
      
      // Enforce Global RTL directionality at the very root of Sela
      builder: (context, child) {
        return Directionality(
          textDirection: TextDirection.rtl,
          child: child!,
        );
      },
      
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

      // Sela Premium Modern Design System (Official Nile Blue & Gold Accent)
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: const Color(0xff192A56),
        scaffoldBackgroundColor: const Color(0xfff8fafc), // Premium soft white background
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xff192A56),
          primary: const Color(0xff192A56),
          secondary: const Color(0xffC5A021),
          surface: Colors.white,
          background: const Color(0xfff8fafc),
        ),
        
        // Cairo Font is the undisputed standard for luxury Arabic interfaces
        textTheme: GoogleFonts.cairoTextTheme(
          Theme.of(context).textTheme,
        ).apply(
          bodyColor: const Color(0xff334155), // Slate dark
          displayColor: const Color(0xff192A56),
        ),
        
        // Modern Transparent AppBars (iOS Luxury Style)
        appBarTheme: AppBarTheme(
          backgroundColor: Colors.transparent,
          foregroundColor: const Color(0xff192A56),
          elevation: 0,
          centerTitle: true,
          iconTheme: const IconThemeData(color: Color(0xff192A56)),
          titleTextStyle: GoogleFonts.cairo(
            fontSize: 20,
            fontWeight: FontWeight.w900,
            color: const Color(0xff192A56),
          ),
        ),

        // Premium Form Fields with soft filled backgrounds & glowing focus indicators
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: const Color(0xfff1f5f9),
          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(20),
            borderSide: BorderSide.none,
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(20),
            borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(20),
            borderSide: const BorderSide(color: Color(0xff192A56), width: 2),
          ),
          labelStyle: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 13),
          hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
        ),
        
        // Luxury Flat Buttons
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xff192A56),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20),
            ),
            elevation: 3,
            shadowColor: const Color(0xff192A56).withOpacity(0.3),
            textStyle: GoogleFonts.cairo(
              fontSize: 15,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ),
      
      home: SplashScreen(isLoggedIn: isLoggedIn),
    );
  }
}
